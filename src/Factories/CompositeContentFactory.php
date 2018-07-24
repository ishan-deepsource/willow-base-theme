<?php

namespace Bonnier\Willow\Base\Factories;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ContentFileAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ContentImageAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\GalleryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\InfoBoxAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\InsertedCodeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\LinkAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\NullContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\TextItemAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\VideoAdapter;

class CompositeContentFactory extends AbstractModelFactory
{
    protected $adapterMapping = [
        'file'          => ContentFileAdapter::class,
        'gallery'       => GalleryAdapter::class,
        'image'         => ContentImageAdapter::class,
        'infobox'       => InfoBoxAdapter::class,
        'inserted_code' => InsertedCodeAdapter::class,
        'link'          => LinkAdapter::class,
        'text_item'     => TextItemAdapter::class,
        'video'         => VideoAdapter::class
    ];
    
    public function getAdapter($model)
    {
        return collect($this->adapterMapping)->get($model['acf_fc_layout'], NullContentAdapter::class);
    }
}
