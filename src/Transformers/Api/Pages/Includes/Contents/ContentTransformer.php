<?php

namespace Bonnier\Willow\Base\Transformers\Api\Pages\Includes\Contents;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;
use Bonnier\Willow\Base\Transformers\Api\Pages\Includes\Contents\Types\BannerPlacementTransformer;
use Bonnier\Willow\Base\Transformers\Api\Pages\Includes\Contents\Types\FeaturedContentTransformer;
use Bonnier\Willow\Base\Transformers\Api\Pages\Includes\Contents\Types\NewsletterTransformer;
use Bonnier\Willow\Base\Transformers\Api\Pages\Includes\Contents\Types\SeoTextTransformer;
use Bonnier\Willow\Base\Transformers\Api\Pages\Includes\Contents\Types\TaxonomyListTransformer;
use Bonnier\Willow\Base\Transformers\Api\Pages\Includes\Contents\Types\TeaserListTransformer;
use Bonnier\Willow\Base\Transformers\NullTransformer;
use Bonnier\WP\ContentHub\Editor\Helpers\AcfName;
use League\Fractal\TransformerAbstract;

class ContentTransformer extends TransformerAbstract
{
    protected $transformerMapping = [
        'teaser_list' => TeaserListTransformer::class,
        'featured_content' => FeaturedContentTransformer::class,
        'seo_text' => SeoTextTransformer::class,
        'newsletter' => NewsletterTransformer::class,
        'banner_placement' => BannerPlacementTransformer::class,
        AcfName::WIDGET_TAXONOMY_TEASER_LIST => TaxonomyListTransformer::class,
    ];
    public function transform(ContentContract $content)
    {
        $transformerClass = collect($this->transformerMapping)->get($content->getType(), NullTransformer::class);
        $transformedData = with(new $transformerClass())->transform($content);
        return array_merge([
            'type'   => $content->getType(),
        ], $transformedData);
    }
}
