<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root\Contents;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;
use Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types\BannerPlacementTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types\CommercialSpotTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types\FeaturedContentTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types\NewsletterTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types\QuoteTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types\SeoTextTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types\TaxonomyListTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types\TeaserListTransformer;
use Bonnier\Willow\Base\Transformers\NullTransformer;
use Bonnier\WP\ContentHub\Editor\Helpers\AcfName;
use League\Fractal\TransformerAbstract;

class ContentTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'content'
    ];

    protected $availableIncludes = [
        'content'
    ];

    protected $transformerMapping = [
        'teaser_list' => TeaserListTransformer::class,
        'featured_content' => FeaturedContentTransformer::class,
        'seo_text' => SeoTextTransformer::class,
        'newsletter' => NewsletterTransformer::class,
        'banner_placement' => BannerPlacementTransformer::class,
        AcfName::WIDGET_TAXONOMY_TEASER_LIST => TaxonomyListTransformer::class,
        AcfName::WIDGET_COMMERCIAL_SPOT => CommercialSpotTransformer::class,
        AcfName::WIDGET_QUOTE_TEASER => QuoteTeaserTransformer::class
    ];

    public function transform(ContentContract $content)
    {
        return [
            'type'   => $content->getType(),
        ];
    }

    public function includeContent(ContentContract $content)
    {
        $transformerClass = collect($this->transformerMapping)->get($content->getType(), NullTransformer::class);
        return $this->item($content, new $transformerClass);
    }

}
