<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\AssociatedContentTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\ContentAudioTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\ContentFileTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\GalleryTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\ContentImageTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\HotspotImageTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\InfoBoxTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\InsertedCodeTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\LeadParagraphTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\LinkTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\ParagraphListTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\QuoteTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\TextItemTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\VideoTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use Bonnier\Willow\Base\Transformers\NullTransformer;
use League\Fractal\TransformerAbstract;

class ContentTransformer extends TransformerAbstract
{

    protected $transformerMapping = [
        'image'                => ContentImageTransformer::class,
        'text_item'            => TextItemTransformer::class,
        'file'                 => ContentFileTransformer::class,
        'gallery'              => GalleryTransformer::class,
        'link'                 => LinkTransformer::class,
        'inserted_code'        => InsertedCodeTransformer::class,
        'video'                => VideoTransformer::class,
        'infobox'              => InfoBoxTransformer::class,
        'associated_composite' => AssociatedContentTransformer::class,
        'audio'                => ContentAudioTransformer::class,
        'quote'                => QuoteTransformer::class,
        'paragraph_list'       => ParagraphListTransformer::class,
        'hotspot_image'        => HotspotImageTransformer::class,
        'lead_paragraph'       => LeadParagraphTransformer::class,
    ];

    public function transform(ContentContract $content)
    {
        $transformerClass = collect($this->transformerMapping)->get($content->getType(), NullTransformer::class);
        $transformedData = with(new $transformerClass())->transform($content);
        return array_merge([
            'type'          => $content->getType(),
            'locked'        => $content->isLocked(),
            'stick_to_next' => $content->getStickToNext()
        ], $transformedData);
    }
}
