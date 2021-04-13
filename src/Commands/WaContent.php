<?php

namespace Bonnier\Willow\Base\Commands;

use Bonnier\Willow\Base\Commands\Helpers\ImportHelper;
use Bonnier\Willow\Base\Commands\Taxonomy\Helpers\WpTerm;
use Bonnier\Willow\Base\Helpers\EstimatedReadingTime;
use Bonnier\Willow\Base\Helpers\HtmlToMarkdown;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\WpAttachment;
use Bonnier\Willow\Base\Models\WpAuthor;
use Bonnier\Willow\Base\Models\WpComposite;
use Bonnier\Willow\Base\Repositories\WhiteAlbum\ContentRepository;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\Cache\Models\Post as BonnierCachePost;
use Bonnier\WP\Cxense\Models\Post as CxensePost;
use Illuminate\Support\Collection;

// temperate solution for running command
use Tests\CompositeContent\Partials\RecipeIngredientBlockItem;
use Tests\CompositeContent\Partials\RecipeIngredientItem;
use Tests\CompositeContent\Partials\RecipeNutrientItem;
use Tests\CompositeContent\Recipe;

use WP_CLI;
use WP_Post;
use WP_User;

/**
 * Class AdvancedCustomFields
 */
class WaContent extends BaseCmd
{
    private const CMD_NAMESPACE = 'wa content';
    private $repository = null;
    private $failedImportFile = null;
    private $isRefreshing = false;
    private $site = null;
    private $waFilesUrl = 'https://files.interactives.dk/';

    public static function register()
    {
        WP_CLI::add_command(CmdManager::CORE_CMD_NAMESPACE.' '.static::CMD_NAMESPACE, __CLASS__);
    }

    /**
     * Prunes imported composites from WhiteAlbum by removing those that are deleted on WhiteAlbum
     *
     * ## OPTIONS
     *
     * [--host=<host>]
     * : Set host name for proper loading of envs
     *
     * ## EXAMPLES
     * wp contenthub editor wa content prune
     *
     * @param $args
     * @param $assocArgs
     *
     * @throws \Exception
     */
    public function prune($args, $assocArgs)
    {
        WpComposite::mapAll(function (WP_Post $post) {
            if ($waId = WpComposite::whiteAlbumIDFromPostID($post->ID)) {
                $repository = new ContentRepository(LanguageProvider::getPostLanguage($post->ID));
                $content    = $repository->findById($waId, ContentRepository::ARTICLE_RESOURCE) ?:
                    $repository->findById($waId, ContentRepository::GALLERY_RESOURCE);
                if ( ! $content) {
                    wp_trash_post($post->ID);
                    WP_CLI::success(sprintf(
                        'Trashed orphaned content: %s id: %s',
                        $post->post_title,
                        $post->ID
                    ));
                }
            }
        });
    }

    /**
     * Fixes wrongly imported headings that was imported as ordered list
     *
     * ## OPTIONS
     *
     * [--host=<host>]
     * : Set host name for proper loading of envs
     *
     * ## EXAMPLES
     * wp contenthub editor wa content fixHeadings
     *
     * @param $args
     * @param $assocArgs
     *
     */
    public function fixHeadings($args, $assocArgs)
    {
        $this->setHost($assocArgs);

        WpComposite::mapAll(function (WP_Post $post) {
            // Get all widgets from composite
            $brokenHeadings = collect(get_field('composite_content', $post->ID))
                ->flatten() // flatten to look at all fields of any widget
                ->reject(function ($value) {
                    if ( ! empty($value) && is_string($value)) {
                        // Match against known pattern
                        preg_match_all('/\d\..+\n-+$/im', $value, $matches);

                        return collect($matches)->flatten()->isEmpty();
                    }

                    return true;
                });
            if ($brokenHeadings->isNotEmpty() && $waId = WpComposite::whiteAlbumIDFromPostID($post->ID)) {
                WP_CLI::warning(sprintf('Will reimport post: %s, id: %d', $post->post_title, $post->ID));

                $repository = new ContentRepository(LanguageProvider::getPostLanguage($post->ID));
                $waContent  = $repository->findById($waId, ContentRepository::ARTICLE_RESOURCE) ?:
                    $repository->findById($waId, ContentRepository::GALLERY_RESOURCE);

                $this->importComposite($waContent);
            } else {
                WP_CLI::line(sprintf('Skipping post: %s', $post->post_title));
            }
        });
        WP_CLI::success('Done fixing headings!');
    }

    /**
     * Imports composites from WhiteAlbum
     *
     * ## OPTIONS
     *
     * [--id=<id>]
     * : The id of a single composite to import.
     *
     * [--type=<type>]
     * : The type of the single composite used together with id, can be article|gallery.
     *
     * [--locale=<locale>]
     * : The locale to fetch from used in conjunction with --id
     *
     * [--page=<page>]
     * : The page to start importing from
     *
     * [--host=<host>]
     * : Set host name for proper loading of envs
     *
     * [--skip-existing]
     * : wether to skip alredy imported articles
     *
     * [--failed-import-file=<failed-import-file>]
     * : The .csv file to save failed imports to
     *
     *
     * ## EXAMPLES
     * wp contenthub editor wa content import
     *
     * @param $args
     * @param $assocArgs
     *
     * @throws \Exception
     */
    public function import($args, $assocArgs)
    {
        error_reporting(E_ALL); // Enable all error reporting to make sure we catch potential issues

        $this->disableHooks(); // Disable various hooks and filters during import

        $this->setHost($assocArgs);

        $this->failedImportFile = $assocArgs['failed-import-file'] ?? null;
        $this->repository       = new ContentRepository($assocArgs['locale'] ?? null, $this->failedImportFile);
        if ($contentId = $assocArgs['id'] ?? null) {
            $resource = collect([
                'article' => ContentRepository::ARTICLE_RESOURCE,
                'gallery' => ContentRepository::GALLERY_RESOURCE,
            ])->get($assocArgs['type'] ?? 'article');
            $this->importComposite($this->repository->findById($contentId, $resource));
        } else {
            $this->repository->mapAll(function ($waContent) {
                $this->importComposite($waContent);
            }, $assocArgs['page'] ?? 1, $assocArgs['skip-existing'] ?? false);
        }
    }

    /**
     * Reruns the import but only refreshes articles from the local
     *
     * ## OPTIONS
     *
     * [--host=<host>]
     * : Set host name for proper loading of envs
     *
     * ## OPTIONS
     *
     * [--id=<id>]
     * : The id of a single composite to import.
     *
     *
     * ## EXAMPLES
     * wp contenthub editor wa content refresh
     *
     * @param $args
     * @param $assocArgs
     *
     * @throws \Exception
     */
    public function refresh($args, $assocArgs)
    {
        $this->setHost($assocArgs);

        $this->isRefreshing = true;
        if ($contentId = $assocArgs['id'] ?? null) {
            if ($post = get_post(WpComposite::postIDFromWhiteAlbumID($contentId))) {
                $this->importComposite($this->getWaContent($post));
            }
        } else {
            WpComposite::mapAll(function (WP_Post $post) {
                $this->importComposite($this->getWaContent($post));
            });
        }
    }

    private function getWaContent(WP_Post $post)
    {
        $waContentJson = get_post_meta($post->ID, WpComposite::POST_META_WHITE_ALBUM_SOURCE, true);
        if ($waContentJson && ($waContent = unserialize($waContentJson))) {
            return $waContent;
        }

        return null;
    }

    private function importComposite($waContent)
    {
        $this->fixNonceErrors();

        if ( ! $waContent) {
            return;
        }

        WP_CLI::line(sprintf(
            'Beginning import of: %s id: %s',
            $waContent->widget_content->title,
            $waContent->widget_content->id
        ));

        $this->site = $waContent->widget_content->site;

        $postId            = $this->createPost($waContent);
        $compositeContents = $this->formatCompositeContents($waContent);

        $this->handleTranslation($postId, $waContent);
        $this->setMeta($postId, $waContent);
        $this->deleteOrphanedFiles($postId, $compositeContents);
        $this->saveCompositeContents($postId, $compositeContents);
        $this->saveTeasers($postId, $waContent);
        $this->saveCategories($postId, $waContent);
        $this->saveTags($postId, $waContent);
        $this->saveOtherAuthors($postId, $waContent);
        $this->calculateReadingTime($postId);

        WP_CLI::success('imported: '.$waContent->widget_content->title.' id: '.$postId);
    }

    private function createPost($waContent)
    {
        $existingId = WpComposite::postIDFromWhiteAlbumID($waContent->widget_content->id);

        if ($existingId) {
            // Prepare for looking up attachments before uploading images necessary for performance
            WpAttachment::get_post_attachments($existingId);
        }

        // Tell Polylang the language of the post to allow multiple posts with the same slug in different languages
        $_POST['post_lang_choice'] = $waContent->widget_content->site->locale;

        return wp_insert_post([
            'ID'            => $existingId,
            'post_title'    => $waContent->widget_content->title,
            'post_name'     => $waContent->slug,
            'post_status'   => $waContent->widget_content->live ? 'publish' : 'draft',
            'post_type'     => WpComposite::POST_TYPE,
            'post_date'     => $waContent->widget_content->publish_at,
            'post_modified' => $waContent->widget_content->publish_at,
            'post_author'   => $this->getFirstAuthor($waContent)->ID,
            'post_category' => [WpTerm::id_from_whitealbum_id($waContent->widget_content->category_id) ?? null],
            'meta_input'    => [
                WpComposite::POST_META_WHITE_ALBUM_ID     => $waContent->widget_content->id,
                WpComposite::POST_META_WHITE_ALBUM_SOURCE => serialize($waContent),
            ],
        ]);
    }

    private function handleTranslation($postId, $waContent)
    {
        LanguageProvider::setPostLanguage($postId, $waContent->widget_content->site->locale);

        //if this is not the master translation, just return
        if ( ! isset($waContent->translation)) {
            return;
        }

        $translationPostIds = collect($waContent->translation->translation_ids)->map(
            function ($translationId, $locale) use ($waContent) {
                $translatedPostId = WpComposite::postIDFromWhiteAlbumID($translationId);
                if ( ! $translatedPostId) {
                    $translatedPostId = $this->importTranslation($translationId, $locale);
                }

                return $translatedPostId;
            }
        )->merge([
            // always push current locale
            $waContent->widget_content->site->locale => $postId,
        ])->rejectNullValues();
        LanguageProvider::savePostTranslations($translationPostIds->toArray());
        if ( ! $translationPostIds->isEmpty()) {
            WP_CLI::success(
                sprintf(
                    'attached the following translations %s to: %s',
                    $translationPostIds,
                    $waContent->widget_content->title
                )
            );
        }
    }

    private function findMatchingTranslation($whiteAlbumId, $locale)
    {
        if ($this->isRefreshing && $post = get_post(WpComposite::postIDFromWhiteAlbumID($whiteAlbumId))) {
            return $this->getWaContent($post);
        }

        $repository = new ContentRepository($locale, $this->failedImportFile);

        return $repository->findById($whiteAlbumId, ContentRepository::ARTICLE_RESOURCE) ?:
            $repository->findById($whiteAlbumId, ContentRepository::GALLERY_RESOURCE);
    }

    private function importTranslation($translationId, $locale)
    {
        if ($translation = $this->findMatchingTranslation($translationId, $locale)) {
            WP_CLI::line(sprintf('found translation: %s in locale: %s', $translation->widget_content->title, $locale));
            $this->importComposite($translation);

            return WpComposite::postIDFromWhiteAlbumID($translationId);
        }
        WP_CLI::warning(sprintf('no translations for %s found.', $translationId));

        return null;
    }

    private function setMeta($postId, $waContent)
    {
        $isShellArticle = isset($waContent->external_link) && ! empty($waContent->external_link);

        update_field('kind', $isShellArticle ? 'Shell' : 'Article', $postId);
        update_field('description', trim($waContent->widget_content->description), $postId);

        update_field('magazine_year', $waContent->magazine_year ?? null, $postId);
        update_field('magazine_issue', $waContent->magazine_number ?? null, $postId);

        update_field('canonical_url', $waContent->widget_content->canonical_link, $postId);
        update_field('internal_comment', $waContent->widget_content->social_media_text, $postId);

        if ($isShellArticle) {
            update_field(CompositeFieldGroup::SHELL_LINK_FIELD, $waContent->external_link, $postId);
        }

        if ($waContent->widget_content->advertorial_label) {
            update_field('commercial', true, $postId);
            $type = join('', array_map(function ($part) {
                return ucfirst($part);
            }, explode(' ', $waContent->widget_content->advertorial_label)));
            update_field('commercial_type', $type ?? null, $postId);
        }
    }

    private function formatCompositeContents($waContent): ?Collection
    {
        if (isset($waContent->body->widget_groups)) {
            return collect($waContent->body->widget_groups)
                ->pluck('widgets')
                ->flatten(1)
                ->map(function ($waWidget) {
                    return collect([
                        'type' => collect([ // Map the type
                            'Widgets::Text'         => 'text_item',
                            'Widgets::Image'        => 'image',
                            'Widgets::InsertedCode' => 'inserted_code',
                            'Widgets::Media'        => 'inserted_code',
                            'Widgets::Info'         => 'infobox',
                            'Widgets::Video'        => 'video',
                            'Widgets::Recipe'       => 'recipe',
                            'Widgets::UploadedFile' => 'file',
                        ])
                            ->get($waWidget->type, null),
                    ])
                        ->merge($waWidget->properties)// merge properties
                        ->merge($waWidget->uploaded_file ?? null)// merge uploaded file
                        ->merge($waWidget->image ?? null); // merge image
                })
                ->prepend(
                    $waContent->widget_content->lead_image ? // prepend lead image
                        collect([
                            'type'       => 'image',
                            'lead_image' => true,
                        ])
                            ->merge($waContent->widget_content->lead_image)
                        : null
                )->itemsToObject()->map(function ($content) {
                    return $this->fixFaultyImageFormats($content);
                });
        }
        if (isset($waContent->gallery_images)) {
            return collect([
                (object) [
                    'type'         => 'gallery',
                    'display_hint' => 'default',
                    'images'       => collect($waContent->gallery_images)
                        ->pluck('image')
                        ->map(function ($waImage) {
                            $waImage->type = 'image';

                            return $this->fixFaultyImageFormats($waImage);
                        }),
                ],
            ]);
        }

        return null;
    }

    private function saveCompositeContents($postId, Collection $compositeContents)
    {
        $content = $compositeContents
            ->map(function ($compositeContent) use ($postId) {
                if ($compositeContent->type === 'text_item') {
                    return [
                        'body'           => HtmlToMarkdown::parseHtml($compositeContent->text),
                        'locked_content' => false,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }
                if ($compositeContent->type === 'image') {
                    return [
                        'lead_image'     => $compositeContent->lead_image ?? false,
                        'file'           => WpAttachment::upload_attachment($postId, $compositeContent),
                        'locked_content' => false,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }
                if ($compositeContent->type === 'infobox') {
                    return [
                        'title'          => $compositeContent->title ?? null,
                        'body'           => HtmlToMarkdown::parseHtml($compositeContent->text),
                        'locked_content' => false,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }

                if ($compositeContent->type === 'file') {
                    $id                             = $compositeContent->uploaded_file_id ?? "";
                    $fileUrl                        = (empty($compositeContent->path)) ? "" : $this->waFilesUrl.$compositeContent->path;
                    $title                          = $compositeContent->title ?? "";
                    $fileObj                        = new \stdClass();
                    $fileObj->id                    = $id;
                    $fileObj->url                   = $fileUrl;
                    $fileObj->title                 = $title;
                    $fileObj->not_generate_metadata = true;
                    $fileId                         = WpAttachment::upload_attachment($postId, $fileObj);

                    return [
                        'title'          => $title,
                        'file'           => $fileId,
                        'locked_content' => false,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }
                if ($compositeContent->type === 'inserted_code') {
                    $insertCode = $compositeContent->code ?? "";
                    if ( ! empty($insertCode) && $this->site->product_code === "IFO") {
                        // only replace in iform
                        $insertCode = ImportHelper::removeInsertCodeEmptyLines($insertCode);
                        $insertCode = ImportHelper::insertCodeWrappingTableClass($insertCode);
                    }

                    return [
                        'code'           => $insertCode,
                        'locked_content' => false,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }
                if ($compositeContent->type === 'gallery') {
                    return [
                        'images'         => $compositeContent->images->map(function ($waImage) use ($postId) {
                            $description = HtmlToMarkdown::parseHtml(
                                sprintf('<h3>%s</h3> %s', $waImage->title, $waImage->description)
                            );
                            // Unset description from image as it will be imported to gallery
                            $waImage->description = null;

                            return [
                                'image'       => WpAttachment::upload_attachment($postId, $waImage),
                                // Prepend title to description as we do not support titles per image
                                'description' => $description,
                            ];
                        }),
                        'display_hint'   => $compositeContent->display_hint,
                        'locked_content' => false,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }
                if ($compositeContent->type === 'video') {
                    return [
                        'embed_url'      => $this->getVideoEmbed(
                            $compositeContent->video_site,
                            $compositeContent->video_id
                        ),
                        'locked_content' => false,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }
                if ($compositeContent->type === 'recipe') {
                    if ( ! isset($compositeContent->active) || $compositeContent->active !== "true") {
                        return [];
                    }

                    $recipe = new Recipe();
                    $recipe->setTitle($compositeContent->title ?? "")
                           ->setDescription('')
                           ->setImage(null)
                           ->setUseAsArticleLeadImage(false)
                           ->setShowMetaInfoInHeaderAndTeaser(true)
                           ->setPreparationTime($compositeContent->prep_headline ?? "")
                           ->setPreparationTimeMin($compositeContent->prep_time ?? "")
                           ->setPreparationTimeUnit(strtolower($compositeContent->prep_unit ?? ""))
                           ->setCookingTime($compositeContent->cook_headline ?? "")
                           ->setCookingTimeMin($compositeContent->cook_time ?? "")
                           ->setCookingTimeUnit(strtolower($compositeContent->cook_unit ?? ""))
                           ->setTotalTime($compositeContent->total_headline ?? "")
                           ->setTotalTimeMin($compositeContent->total_time ?? "")
                           ->setTotalTimeUnit(strtolower($compositeContent->total_unit ?? ""))
                           ->setTotalTimeExtraInfo($compositeContent->total_time_extra ?? "")
                           ->setQuantity($compositeContent->recipe_yield_value ?? "")
                           ->setQuantityType($compositeContent->recipe_yield_text ?? "");

                    //Convert WA ingredients content: "150]];[[1]];[[laks||;||200]];[[1]];[[fuldkornspasta||;||200]];[[2]];[[grønne asparges||;||1]];[[16]];[[dildspidser||;||2 ]];[[3]];[[jomfruolivenolie||;||2]];[[4]];[[parmesan||;||]];[[]];[[havsalt||;||]];[[]];[[sort peber" to recipe block items
                    //Row separator is "||;||", item separator is "]];[["
                    $waIngredients = $compositeContent->ingredients;
                    if ( ! empty($waIngredients)) {
                        $ingredientsChoices = array_keys(CompositeFieldGroup::getRecipeIngredientChoices());
                        $ingredientsRows    = explode("||;||", $waIngredients);
                        $firstBlock         = true;
                        $ingredientBlocks   = [];
                        $newBlock           = null;
                        foreach ($ingredientsRows as $ingredientsRow) {
                            $items = explode("]];[[", $ingredientsRow);
                            if (count($items) == 1) {
                                $headLine = $items[0];
                                if ( ! $firstBlock) {
                                    $ingredientBlocks[] = $newBlock;
                                }
                                $newBlock = new RecipeIngredientBlockItem($headLine, []);
                            } else {
                                if ($newBlock == null) {
                                    $newBlock = new RecipeIngredientBlockItem();
                                }
                                $newBlock->addIngredientItem(new RecipeIngredientItem($items[0] ?? "",
                                    $ingredientsChoices[$items[1]] ?? "",
                                    $items[2] ?? ""));
                            }

                            $firstBlock = false;
                            // for last element
                            if ($ingredientsRow === end($ingredientsRows)) {
                                $ingredientBlocks[] = $newBlock;
                            }
                        }

                        // Add ingredient blocks to recipe
                        foreach ($ingredientBlocks as $ingredientBlock) {
                            $recipe->addIngredientBlockItem($ingredientBlock);
                        }
                    }

                    //separate instruction and instruction tip
                    $delimiters         = ['tips', 'tip', 'vinkki'];
                    $instructionTextRaw = HtmlToMarkdown::parseHtml($compositeContent->instructions ?? "");
                    $instructionText    = $instructionTextRaw;
                    $instructionTip     = '';
                    if ( ! empty($instructionTextRaw)) {
                        foreach ($delimiters as $delimiter) {
                            $instructionArr = preg_split("/\*\*".$delimiter."/i", $instructionTextRaw, 2);
                            if (count($instructionArr) == 2) {
                                $instructionText = $instructionArr[0];
                                $instructionTip  = "**".strtoupper($delimiter).$instructionArr[1];
                                break;
                            }
                        }
                    }

                    $recipe->setInstructionsHeadline($compositeContent->instructions_headline ?? "")
                           ->setInstructions($instructionText)
                           ->setInstructionsTip($instructionTip)
                           ->setNutrientsHeadline($compositeContent->nutrients_headline ?? "");

                    //Convert WA nutrients content: 0]];[[589]];[[1||;||1]];[[36,3]];[[2||;||2]];[[17,2]];[[2||;||3]];[[78,1]];[[2||;||4]];[[11,5]];[[2
                    $waIngredientsRows = $compositeContent->nutrients;
                    if ( ! empty($waIngredientsRows)) {
                        $nutrientItemsChoices     = array_keys(CompositeFieldGroup::getRecipeNutrientItemsChoices());
                        $nutrientItemsUnitChoices = array_keys(CompositeFieldGroup::getRecipeNutrientItemsUnitChoices());
                        $nutrientsRows            = explode("||;||", $waIngredientsRows);
                        $nutrientsCollection      = [];
                        foreach ($nutrientsRows as $nutrientsRow) {
                            $nutrientsItems        = explode("]];[[", $nutrientsRow);
                            $nutrientsCollection[] = new RecipeNutrientItem($nutrientItemsChoices[$nutrientsItems[0]] ?? "",
                                $nutrientsItems[1] ?? "", $nutrientItemsUnitChoices[$nutrientsItems[2]] ?? "");
                        }
                        $recipe->setNutrientItems(new Collection($nutrientsCollection));
                    }

                    $recipe->setTags($compositeContent->tags ?? "");

                    $data                   = $recipe->toArray();
                    $data['acf_fc_layout']  = $compositeContent->type;
                    $data['locked_content'] = false;

                    // if the article contains recipe widget, so it is a recipe template
                    update_post_meta($postId, '_wp_page_template', 'recipe');

                    return $data;
                }

                return null;
            })->rejectNullValues();
        update_field('composite_content', $content->toArray(), $postId);
    }

    private function saveTags($postId, $waContent)
    {
        $tagIds = collect($waContent->widget_content->tags)->map(function ($waTag) {
            return WpTerm::id_from_whitealbum_id($waTag->id) ?: null;
        })->rejectNullValues();
        update_field('tags', $tagIds->toArray(), $postId);
    }

    private function saveTeasers($postId, $waContent)
    {
        $teaserTitle       = $waContent->widget_content->teaser_title ?: $waContent->widget_content->title;
        $teaserDescription = $waContent->widget_content->teaser_description ?: $waContent->widget_content->description;
        $teaserImage       = $waContent->widget_content->teaser_image ?: $waContent->widget_content->lead_image;

        // General teaser
        update_field(WpComposite::POST_TEASER_TITLE, $teaserTitle, $postId);
        update_field(WpComposite::POST_TEASER_DESCRIPTION, $teaserDescription, $postId);
        update_field(WpComposite::POST_TEASER_IMAGE, WpAttachment::upload_attachment($postId, $teaserImage), $postId);

        // Facebook teaser
        if ($waContent->widget_content->teaser_facebook_only) {
            update_field(WpComposite::POST_FACEBOOK_TITLE, $teaserTitle, $postId);
            update_field(WpComposite::POST_FACEBOOK_DESCRIPTION, $teaserDescription, $postId);
            update_field(WpComposite::POST_FACEBOOK_IMAGE, WpAttachment::upload_attachment(
                $postId,
                $teaserImage
            ), $postId);
        }

        update_field(WpComposite::POST_META_TITLE, $waContent->widget_content->meta_title, $postId);
        update_field(WpComposite::POST_META_DESCRIPTION, $waContent->widget_content->meta_description, $postId);
    }

    private function saveCategories($postId, $composite)
    {
        if ($existingTermId = WpTerm::id_from_whitealbum_id($composite->widget_content->category_id)) {
            update_field('category', $existingTermId, $postId);
        }
    }

    private function saveOtherAuthors($postId, $waContent)
    {
        $otherAuthors = $this->getOtherAuthors($waContent);
        if (!empty($otherAuthors)) {
            update_post_meta($postId, 'other_authors', $otherAuthors);
        }
    }

    /**
     * @param                                $postId
     * @param  Collection  $compositeContents
     *
     * Deletes attachments that would have otherwise become orphaned after import
     */
    private function deleteOrphanedFiles($postId, Collection $compositeContents)
    {
        $currentFileIds = collect(get_field('composite_content', $postId))
            ->map(function ($content) use ($postId) {
                if ($content['acf_fc_layout'] === 'image') {
                    return WpAttachment::contenthub_id($content['file'] ?? null);
                }
                if ($content['acf_fc_layout'] === 'file') {
                    return [
                        'file'   => WpAttachment::contenthub_id($content['file'] ?? null),
                        'images' => collect($content['images'])->map(function ($image) {
                            return WpAttachment::contenthub_id($image['file'] ?? null);
                        }),
                    ];
                }
                if ($content['acf_fc_layout'] === 'gallery') {
                    return collect($content['images'])->map(function ($galleryItem) {
                        return WpAttachment::contenthub_id($galleryItem['image'] ?? null);
                    });
                }

                return null;
            })->flatten()
            ->push(WpAttachment::contenthub_id(get_field('teaser_image', $postId)))
            ->rejectNullValues();


        $newFileIds = $compositeContents
            ->map(function ($compositeContent) {
                if ($compositeContent->type === 'image') {
                    return $compositeContent->id;
                }
                if ($compositeContent->type === 'gallery') {
                    return $compositeContent->images->map(function ($galleryItem) {
                        return $galleryItem->id;
                    });
                }

                return null;
            })
            ->flatten()
            ->rejectNullValues();

        // Compare current file ids to new file ids
        $currentFileIds->diff($newFileIds)->each(function ($orphanedFileId) {
            // We delete any of the current files that would be come orphaned
            WpAttachment::delete_by_contenthub_id($orphanedFileId);
        });
    }

    private function disableHooks()
    {
        // Disable generation of image sizes on import to speed up the precess
        add_filter('intermediate_image_sizes_advanced', function ($sizes) {
            return [];
        });

        // Disable on save hook to prevent call to content hub, Cxense and Bonnier Cache Manager
        remove_action('save_post', [WpComposite::class, 'on_save'], 10);
        remove_action('publish_to_publish', [BonnierCachePost::class, 'update_post'], 10);
        remove_action('draft_to_publish', [BonnierCachePost::class, 'publishPost'], 10);
        remove_action('transition_post_status', [CxensePost::class, 'post_status_changed'], 10);
    }

    private function getAuthor($waContent, $authorName): WP_User
    {
        if (!empty($authorName)) {
            $author = WpAuthor::findOrCreate($authorName);
            if ($author instanceof WP_User) {
                return $author;
            }
        }

        return WpAuthor::getDefaultAuthor($waContent->widget_content->site->locale);
    }

    private function getFirstAuthor($waContent)
    {
        $authors = $waContent->widget_content->authors;
        if (!empty($authors)) {
            return $this->getAuthor($waContent, $authors[0]->name);
        }

        return WpAuthor::getDefaultAuthor($waContent->widget_content->site->locale);
    }

    private function getOtherAuthors($waContent)
    {
        $output = [];
        $authors = $waContent->widget_content->authors;
        if (!empty($authors) && count($authors) > 1) {
            array_shift($authors);
            foreach ($authors as $authorKey => $authorValue) {
                $output[] = $this->getAuthor($waContent, $authorValue->name)->ID;
            }
        }

        return $output;
    }

    private function fixFaultyImageFormats($content)
    {
        if (
            $content->type === 'image' &&
            ($extension = pathinfo($content->url, PATHINFO_EXTENSION)) &&
            in_array($extension, ['psd'])
        ) {
            // If we find the image extension to be in the blacklist then we tell imgix to return as png format
            $content->url .= '?fm=png';
        }

        return $content;
    }

    private function getVideoEmbed($provider, $videoId)
    {
        $vendor = collect([
            'youtube' => 'https://www.youtube.com/embed/',
            'vimeo'   => 'https://player.vimeo.com/video/',
            'video23' => 'https://'.
                         ($this->site->video23_account ?? 'bonnier-publications-danmark').
                         '.23video.com/v.ihtml/player.html?source=share&photo%5fid=',
        ])->get($provider);

        return $vendor ? $vendor.$videoId : null;
    }

    private function fixNonceErrors()
    {
        wp_set_current_user(1); // Make sure we act as admin to allow upload of all file types
        $_REQUEST['_pll_nonce'] = wp_create_nonce('pll_language');
    }

    private function calculateReadingTime($postId)
    {
        EstimatedReadingTime::addEstimatedReadingTime($postId);
    }
}
