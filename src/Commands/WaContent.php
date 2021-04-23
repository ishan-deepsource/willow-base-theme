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
    private const RELATED_CONTENT_ID_NAME = "related_content_Ids";
    private const STORY_ITEMS_ID_NAME = "story_items_id_name";

    public static function register()
    {
        WP_CLI::add_command(CmdManager::CORE_CMD_NAMESPACE.' '.static::CMD_NAMESPACE, __CLASS__);
    }

    /**
     * Prunes imported composites from WhiteAlbum by removing those that are deleted on WhiteAlbum
     * It is normal that print some warnings if there cannot find WA articles, stories, galleries on WA.
     *
     * ## OPTIONS
     *
     * [--host=<host>]
     * : Set host name for proper loading of envs
     *
     * ## EXAMPLES
     * wp contenthub editor wa content prune --host=iform.dk --allow-root
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
                $content = $this->getWaContentByWpPostId($post->ID);
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
            if ($brokenHeadings->isNotEmpty()) {
                $waContent = $this->getWaContentByWpPostId($post->ID);
                WP_CLI::warning(sprintf('Will reimport post: %s, id: %d', $post->post_title, $post->ID));
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
     * : The id of a single composite to import. It is id under widget_content (not id in the top) from wa api output.
     *
     * [--type=<type>]
     * : The type of the single composite used together with id, can be article|gallery|story.
     *
     * [--locale=<locale>]
     * : The locale to fetch from used in conjunction with --id, can be da|nb|sv|fi
     *
     * [--page=<page>]
     * : The page to start importing from
     *
     * [--host=<host>]
     * : Set host name for proper loading of envs
     *
     * [--skip-existing]
     * : weather to skip already imported articles
     *
     * [--failed-import-file=<failed-import-file>]
     * : The .csv file to save failed imports to
     *
     *
     * ## EXAMPLES
     *  wp contenthub editor wa content import --allow-root --host=iform.dk --id=1000266579 --locale=da
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
            $resource = collect(ContentRepository::getWaResources())->get(ucfirst($assocArgs['type'] ?? 'article'));
            // import one content
            $this->importComposite($this->repository->findById($contentId, $resource));
        } else {
            // import all contents
            $this->repository->mapAll(
                function ($waContent) {
                    $this->importComposite($waContent);
                },
                $assocArgs['page'] ?? 1,
                $assocArgs['skip-existing'] ?? false);
        }
    }

    private function importComposite($waContent)
    {
        $this->fixNonceErrors();

        if ( ! $waContent) {
            return;
        }

        WP_CLI::line(sprintf(
            'Beginning import of: %s widget_content id: %s',
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
        $this->saveAuthorDescription($postId, $waContent);
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

    private function formatCompositeContents($waContent): ?Collection
    {
        // format gallery type
        if ($this->getWaContentType($waContent) === 'Gallery') {
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

        // format story and article type
        // story type don't have body attribute
        $storyItems = false;
        $theme      = '';
        if ($this->getWaContentType($waContent) === 'Story') {
            $widgetGroups = $waContent->widget_groups;
            $storyItems   = $waContent->story_items ?? false;
            $theme        = $waContent->theme ?? '';
        } else {
            $widgetGroups = $waContent->body->widget_groups;
        }

        $relatedContents = $waContent->widget_content->related_widget_contents ?? false;
        if (isset($widgetGroups)) {
            $compositeContentsCollection = collect($widgetGroups)
                ->pluck('widgets')
                ->flatten(1)
                ->push($storyItems ? (object) ['type' => 'Custom:StoryItems'] : null)
                ->rejectNullValues()
                ->map(function ($waWidget) use ($relatedContents, $storyItems, $theme) {
                    return collect([
                        'type' => collect([ // Map the type
                            'Widgets::Text'           => 'text_item',
                            'Widgets::Image'          => 'image',
                            'Widgets::InsertedCode'   => 'inserted_code',
                            'Widgets::Media'          => 'inserted_code',
                            'Widgets::Info'           => 'infobox',
                            'Widgets::Video'          => 'video',
                            'Widgets::Recipe'         => 'recipe',
                            'Widgets::UploadedFile'   => 'file',
                            'Widgets::RelatedContent' => 'associated_composites',
                            // Custom:StoryItems is not exist in WA
                            'Custom:StoryItems'       => 'associated_composites_story',
                        ])
                            ->get($waWidget->type ?? null, null),
                    ])
                        ->merge($waWidget->properties ?? null)// merge properties
                        ->merge($waWidget->uploaded_file ?? null)// merge uploaded file
                        ->merge($waWidget->image ?? null)
                        // build story widget
                        ->merge($storyItems && $waWidget->type === 'Custom:StoryItems' ? [
                            self::STORY_ITEMS_ID_NAME => collect($storyItems)->pluck('widget_content_id'),
                            'title'                   => $theme,
                        ] : null)
                        ->merge($relatedContents && $waWidget->type === 'Widgets::RelatedContent' ? [self::RELATED_CONTENT_ID_NAME => collect($relatedContents)->pluck('id')] : null); // merge related content ids
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

            // Related content need to be only one and at the end of articles/story/gallery
            // Keep only first related content (associated_composites widget) and move it to end of array
            $isFirstRelatedContent       = true;
            $compositeContentsCollection = $compositeContentsCollection->reject(
                function ($widgetObj) use (&$relatedContentWidget, &$isFirstRelatedContent) {
                    if ($widgetObj->type == 'associated_composites') {
                        if ($isFirstRelatedContent) {
                            $relatedContentWidget = $widgetObj;
                        }
                        $isFirstRelatedContent = false;

                        return true;
                    }

                    return false;
                })->push($relatedContentWidget ?? null)->rejectNullValues();

            return $compositeContentsCollection;
        }

        return null;
    }

    private function saveCompositeContents($postId, ?Collection $compositeContents): void
    {
        if ($compositeContents === null) {
            return;
        }

        $content               = $compositeContents
            ->map(function ($compositeContent) use ($postId) {
                if ($compositeContent->type === 'text_item' && ! empty($compositeContent->text ?? null)) {
                    $text = $compositeContent->text;
                    // only replace in iform
                    if ($this->site->product_code === "IFO") {
                        $text = ImportHelper::fixFloatingTextsWithoutParagraphTag($text);
                        //removeEmptyLines function must under fixFloatingTextsWithoutParagraphTag
                        $text = ImportHelper::removeEmptyLines($text);
                    }

                    return [
                        'body'           => HtmlToMarkdown::parseHtml($text),
                        'locked_content' => false,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }
                if ($compositeContent->type === 'image' && ! empty($compositeContent->url ?? null)) {
                    $leadImage = $compositeContent->lead_image ?? false;
                    //ifo will have small image (sm), if it is not lead image
                    $displayHint = ( ! $leadImage && $this->site->product_code === "IFO") ? 'sm' : 'default';

                    return [
                        'lead_image'     => $compositeContent->lead_image ?? false,
                        'file'           => WpAttachment::upload_attachment($postId, $compositeContent),
                        'locked_content' => false,
                        'display_hint'   => $displayHint,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }
                if ($compositeContent->type === 'infobox' && ! empty($compositeContent->text ?? null)) {
                    return [
                        'title'          => $compositeContent->title ?? null,
                        'body'           => HtmlToMarkdown::parseHtml($compositeContent->text),
                        'locked_content' => false,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }
                if ($compositeContent->type === 'file' && ! empty($compositeContent->path ?? null)) {
                    $id             = $compositeContent->uploaded_file_id ?? "";
                    $fileUrl        = $this->waFilesUrl.$compositeContent->path;
                    $title          = $compositeContent->title ?? "";
                    $fileObj        = new \stdClass();
                    $fileObj->id    = $id;
                    $fileObj->url   = $fileUrl;
                    $fileObj->title = $title;
                    $fileId         = WpAttachment::upload_attachment($postId, $fileObj);

                    return [
                        # will not migrate file title from wa
                        'title'          => '',
                        'file'           => $fileId,
                        'locked_content' => false,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }
                if ($compositeContent->type === 'inserted_code' && ! empty($insertCode = $compositeContent->code ?? null)) {
                    if ($this->site->product_code === "IFO") {
                        // only replace in iform
                        $insertCode = ImportHelper::removeEmptyLines($insertCode);
                        $insertCode = ImportHelper::insertCodeWrappingTableClass($insertCode);
                    }

                    return [
                        'code'           => $insertCode,
                        'locked_content' => false,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }
                if ($compositeContent->type === 'gallery' && ! empty($compositeContent->images ?? null)) {
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

                //related content widget
                if ($compositeContent->type === 'associated_composites'  && ! empty($relatedContentIds = $compositeContent->{self::RELATED_CONTENT_ID_NAME} ?? null)) {
                    $associateArticles     = new Collection();
                    foreach ($relatedContentIds as $id) {
                        $post = get_post(WpComposite::postIDFromWhiteAlbumID($id));
                        if (isset($post->ID)) {
                            $associateArticles->push($post->ID);
                        } else {
                            WP_CLI::warning(sprintf('Associated composites cannot find wp post with wa id: %s. Please import it first.',
                                $id));
                        }
                    }

                    return [
                        'title'          => $compositeContent->title ?? '',
                        'composites'     => $associateArticles->toArray(),
                        'locked_content' => false,
                        'acf_fc_layout'  => $compositeContent->type,
                    ];
                }
                // associated_composites_story is not a widget, it is associated composites alias
                if ($compositeContent->type === 'associated_composites_story' && ! empty($relatedContentIds = $compositeContent->{self::STORY_ITEMS_ID_NAME} ?? null)) {
                    $associateArticles = new Collection();
                    foreach ($relatedContentIds as $id) {
                        $post = get_post(WpComposite::postIDFromWhiteAlbumID($id));
                        if (isset($post->ID)) {
                            $associateArticles->push($post->ID);
                        } else {
                            WP_CLI::warning(sprintf('Story associated composites cannot find wp post with wa id: %s. Please import it first.',
                                $id));
                        }
                    }
                    // need to change content to Story, if there has associated_composites_story
                    update_post_meta($postId, 'kind', 'Story');

                    return [
                        'title'          => $compositeContent->title ?? '',
                        'composites'     => $associateArticles->toArray(),
                        'display_hint'   => 'story-list',
                        'locked_content' => true,
                        'acf_fc_layout'  => 'associated_composites',
                    ];
                }
                if ($compositeContent->type === 'recipe' && isset($compositeContent->active) && $compositeContent->active === "true") {
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
                            if (count($items) === 1) {
                                $headLine = $items[0];
                                if ( ! $firstBlock) {
                                    $ingredientBlocks[] = $newBlock;
                                }
                                $newBlock = new RecipeIngredientBlockItem($headLine, []);
                            } else {
                                if ($newBlock === null) {
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
                            if (count($instructionArr) === 2) {
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

    private function handleTranslation($postId, $waContent)
    {
        LanguageProvider::setPostLanguage($postId, $waContent->widget_content->site->locale);

        //if this is not the master translation, just return
        if ( ! isset($waContent->translation)) {
            return;
        }

        $waContentType = $this->getWaContentType($waContent);

        $translationPostIds = collect($waContent->translation->translation_ids)->map(
            function ($translationId, $locale) use ($waContentType) {
                $translatedPostId = WpComposite::postIDFromWhiteAlbumID($translationId);
                if ( ! $translatedPostId) {
                    $translatedPostId = $this->importTranslation($translationId, $locale, $waContentType);
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

    /**
     * Get wa content by wp post id
     * It will loop all wa content types, if it cannot get wa content by a wa content type, it will print out warning
     * message, which it is normal.
     *
     * @param $postId
     *
     * @return array|false|mixed|object
     * @throws \Exception
     */
    private function getWaContentByWpPostId($postId)
    {
        $waResourceTypes = ContentRepository::getWaResources();
        $waId            = WpComposite::whiteAlbumIDFromPostID($postId);
        if ( ! $waId) {
            return false;
        }
        foreach ($waResourceTypes as $type) {
            $repository = new ContentRepository(LanguageProvider::getPostLanguage($postId));
            $content    = $repository->findById($waId, $type) ?? false;
            if ($content) {
                return $content;
            }
        }

        return false;
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

    /**
     * Get wa content type
     *
     * @param $waContent
     *
     * @return string
     */
    private function getWaContentType($waContent)
    {
        $waContentMappings = ContentRepository::getWaContentTypeMapping();

        foreach ($waContentMappings as $contentType => $waContentMapping) {
            if (property_exists($waContent, $waContentMapping)) {
                return $contentType;
            }
        }

        return '';
    }

    private function importTranslation($translationId, $locale, $contentType)
    {
        if ($translation = $this->findMatchingTranslation($translationId, $locale, $contentType)) {
            WP_CLI::line(sprintf('found translation: %s in locale: %s', $translation->widget_content->title, $locale));
            $this->importComposite($translation);

            return WpComposite::postIDFromWhiteAlbumID($translationId);
        }
        WP_CLI::warning(sprintf('no translations for %s found.', $translationId));

        return null;
    }

    private function findMatchingTranslation($whiteAlbumId, $locale, $waContentType)
    {
        if ($this->isRefreshing && $post = get_post(WpComposite::postIDFromWhiteAlbumID($whiteAlbumId))) {
            return $this->getWaContent($post);
        }

        $repository      = new ContentRepository($locale, $this->failedImportFile);
        $waResourceTypes = ContentRepository::getWaResources();

        // if there has a resource type
        if (array_key_exists($waContentType, $waResourceTypes) && $waContent = $repository->findById($whiteAlbumId,
                $waResourceTypes[$waContentType])) {
            return $waContent;
        }

        // if don't have wa content type, tries to loop all resource types, for getting wa content
        foreach ($waResourceTypes as $waResourceType) {
            if ($waContent = $repository->findById($whiteAlbumId, $waResourceType)) {
                return $waContent;
            }
        }

        return false;
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
        if ( ! empty($otherAuthors)) {
            update_post_meta($postId, WpComposite::POST_OTHER_AUTHORS, $otherAuthors);
        }
    }

    private function saveAuthorDescription($postId, $waContent)
    {
        update_field(WpComposite::POST_AUTHOR_DESCRIPTION, $waContent->widget_content->authors_appendix, $postId);
    }

    /**
     * Deletes attachments that would have otherwise become orphaned after import
     *
     * @param $postId
     * @param  Collection|null  $compositeContents
     */
    private function deleteOrphanedFiles($postId, ?Collection $compositeContents)
    {
        if ($compositeContents == null) {
            return;
        }

        $currentFileIds = collect(get_field('composite_content', $postId))
            ->map(function ($content) {
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
        if ( ! empty($authorName)) {
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
        if ( ! empty($authors)) {
            return $this->getAuthor($waContent, $authors[0]->name);
        }

        return WpAuthor::getDefaultAuthor($waContent->widget_content->site->locale);
    }

    private function getOtherAuthors($waContent)
    {
        $output  = [];
        $authors = $waContent->widget_content->authors;
        if ( ! empty($authors) && count($authors) > 1) {
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
