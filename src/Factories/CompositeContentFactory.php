<?php

namespace Bonnier\Willow\Base\Factories;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\AssociatedCompositesAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ChaptersSummaryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ContentAudioAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ContentFileAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ContentImageAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\GalleryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\HotspotImageAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\InfoBoxAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\InsertedCodeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\LeadParagraphAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\LinkAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\NewsletterAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\NullContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ParagraphListAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\QuoteAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\TextItemAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\VideoAdapter;

class CompositeContentFactory extends AbstractModelFactory
{
    protected $adapterMapping = [
        'file'                  => ContentFileAdapter::class,
        'gallery'               => GalleryAdapter::class,
        'image'                 => ContentImageAdapter::class,
        'infobox'               => InfoBoxAdapter::class,
        'inserted_code'         => InsertedCodeAdapter::class,
        'link'                  => LinkAdapter::class,
        'text_item'             => TextItemAdapter::class,
        'video'                 => VideoAdapter::class,
        'audio'                 => ContentAudioAdapter::class,
        'quote'                 => QuoteAdapter::class,
        'associated_composites' => AssociatedCompositesAdapter::class,
        'paragraph_list'        => ParagraphListAdapter::class,
        'hotspot_image'         => HotspotImageAdapter::class,
        'lead_paragraph'        => LeadParagraphAdapter::class,
        'newsletter'            => NewsletterAdapter::class,
        'chapters_summary'      => ChaptersSummaryAdapter::class,
    ];

    public function getAdapter($model)
    {
        return collect($this->adapterMapping)->get($model['acf_fc_layout'], NullContentAdapter::class);
    }
}
