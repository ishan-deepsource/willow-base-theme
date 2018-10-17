<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\AssociatedContentContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\ContentAudioTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Vocabulary\VocabularyTransformer;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
use Bonnier\WP\Cxense\Parsers\Document;
use Bonnier\WP\Cxense\Services\WidgetDocumentQuery;
use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Helpers\Cache;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\ContentTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\AuthorTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\CommercialTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\TeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Category\CategoryTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Tag\TagTransformer;
use Bonnier\WP\Cxense\WpCxense;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;
use WP_Post;

class CompositeTransformer extends TransformerAbstract
{
    use UrlTrait;

    protected $originalResponseData;

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'contents',
        'category',
        'related',
        'teasers',
        'tags',
        'vocabularies',
        'associated_content'
    ];

    protected $defaultIncludes = [
        'category'
    ];

    /**
     * CategoryTransformer constructor.
     *
     * @param $originalResponseData
     */
    public function __construct(array $originalResponseData = [])
    {
        $this->originalResponseData = $originalResponseData;
    }

    public function transform(CompositeContract $composite)
    {
        return [
            'id'                        => $composite->getId(),
            'title'                     => $composite->getTitle(),
            'description'               => $composite->getDescription(),
            'status'                    => $composite->getStatus(),
            'locale'                    => $composite->getLocale(),
            'commercial'                => $this->getCommercial($composite),
            'author'                    => $this->getAuthor($composite),
            'author_description'        => $composite->getAuthorDescription() ?: null,
            'lead_image'                => $this->getLeadImage($composite),
            'published_at'              => $composite->getPublishedAt(),
            'updated_at'                => $composite->getUpdatedAt(),
            'canonical_url'             => $composite->getCanonicalUrl(),
            'template'                  => $composite->getTemplate(),
            'estimated_reading_time'    => $composite->getEstimatedReadingTime(),
            'audio'                     => $this->getAudio($composite),
            'word_count'                => $composite->getWordCount(),
        ];
    }

    public function includeContents(CompositeContract $composite)
    {
        return $this->collection($composite->getContents(), new ContentTransformer());
    }

    public function includeVocabularies(CompositeContract $composite)
    {
        return $this->collection($composite->getVocabularies(), new VocabularyTransformer());
    }

    public function includeCategory(CompositeContract $composite)
    {
        $category = $composite->getCategory();
        return $category ? $this->item($category, new CategoryTransformer()) : null;
    }

    public function includeTags(CompositeContract $composite)
    {
        return $this->collection($composite->getTags(), new TagTransformer());
    }

    public function includeTeasers(CompositeContract $composite)
    {
        return $this->collection($composite->getTeasers(), new TeaserTransformer());
    }

    private function getCommercial(CompositeContract $composite)
    {
        if ($commercial = $composite->getCommercial()) {
            return with(new CommercialTransformer())->transform($commercial);
        }
        return null;
    }

    private function getAuthor(CompositeContract $composite)
    {
        if ($author = $composite->getAuthor()) {
            return with(new AuthorTransformer())->transform($author);
        }

        return null;
    }

    private function getAudio(CompositeContract $composite)
    {
        if ($audio = $composite->getAudio()) {
            return with(new ContentAudioTransformer())->transform($audio);
        }
        return null;
    }

    private function getLeadImage(CompositeContract $composite)
    {
        if ($leadImage = $composite->getLeadImage()) {
            return $leadImage->isLocked() ? $leadImage->getId() : with(new ImageTransformer())->transform($leadImage);
        }

        return null;
    }

    public function includeRelated(CompositeContract $composite, ParamBag $paramBag)
    {
        if (($tag = $paramBag->get('tag')) && !empty($tag)) {
            return $this->relatedFromTags($tag[0]);
        }

        return $this->relatedFromCxense($composite);
    }

    private function relatedFromTags(string $tag)
    {
        $cacheKey = sprintf('related_composites_by_tag_%s', $tag);
        $expiresIn = 10 * HOUR_IN_SECONDS;
        $content = Cache::remember($cacheKey, $expiresIn, function () use ($tag) {
            $query = new \WP_Query([
                'post_type' => WpComposite::POST_TYPE,
                'post_status' => 'publish',
                'tag' => $tag
            ]);
            if ($query->post_count) {
                return collect($query->posts)->map(function (WP_Post $post) {
                    return new Composite(new CompositeAdapter($post));
                })->toArray();
            }
            return null;
        });

        return $this->collection($content, new CompositeTeaserTransformer());
    }

    private function relatedFromCxense(CompositeContract $composite)
    {
        //Cache is handled inside cxense plugin
        $result = WidgetDocumentQuery::make()
            ->addContext('url', $this->getFullUrl($composite->getLink()))
            ->byRelated()
            ->addParameter('pageType', 'article gallery story')
            ->setCategories()
            ->get();
        $content = collect($result['matches'])->map(
            function (Document $cxArticle) use ($composite) {
                $locale = WpCxense::instance()->settings->getOrganisationPrefix(LanguageProvider::getCurrentLanguage('locale')) ?? 'da';
                if ($composite->getCommercial() && $cxArticle->{$locale. '-commercial-label'}) {
                    return null;
                }
                $postId = intval($cxArticle->{'recs-articleid'});
                $post = get_post($postId);
                return $post && $post->post_status === 'publish' && $post->ID === $postId
                ? new Composite(new CompositeAdapter($post)) :
                    null;
            }
        )->reject(function ($content) {
            return is_null($content);
        })->toArray();

        return $this->collection($content, new CompositeTeaserTransformer());
    }

    public function includeAssociatedContent(CompositeContract $composite)
    {
        if ($associatedContents = $composite->getAssociatedComposites()) {
            return $this->collection($associatedContents->map(function (AssociatedContentContract $associatedContent) {
                return $associatedContent->getAssociatedComposite();
            })->reject(function ($composite) {
                return is_null($composite);
            }), new CompositeTeaserTransformer);
        }

        return null;
    }
}
