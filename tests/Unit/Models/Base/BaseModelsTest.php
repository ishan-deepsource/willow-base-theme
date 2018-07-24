<?php

namespace Tests\Bonnier\WpVue\Unit\Models\Base;

use Bonnier\WpVue\Models\Base\Terms\Tag;
use Tests\Bonnier\WpVue\Unit\ClassTestCase;
use Bonnier\WpVue\Models\Base\Composites\Composite;
use Bonnier\WpVue\Models\Base\Composites\Contents\Types\ContentFile;
use Bonnier\WpVue\Models\Base\Composites\Contents\Types\ContentImage;
use Bonnier\WpVue\Models\Base\Composites\Contents\Types\Gallery;
use Bonnier\WpVue\Models\Base\Composites\Contents\Types\InfoBox;
use Bonnier\WpVue\Models\Base\Composites\Contents\Types\InsertedCode;
use Bonnier\WpVue\Models\Base\Composites\Contents\Types\Link;
use Bonnier\WpVue\Models\Base\Composites\Contents\Types\TextItem;
use Bonnier\WpVue\Models\Base\Composites\Contents\Types\Video;
use Bonnier\WpVue\Models\Base\Pages\Page;
use Bonnier\WpVue\Models\Base\Root\Author;
use Bonnier\WpVue\Models\Base\Root\Commercial;
use Bonnier\WpVue\Models\Base\Root\File;
use Bonnier\WpVue\Models\Base\Root\Image;
use Bonnier\WpVue\Models\Base\Root\MenuItem;
use Bonnier\WpVue\Models\Base\Root\SitemapCollection;
use Bonnier\WpVue\Models\Base\Root\SitemapItem;
use Bonnier\WpVue\Models\Base\Root\Teaser;
use Bonnier\WpVue\Models\Base\Terms\Category;

class BaseModelsTest extends ClassTestCase
{
    public function testModelsImplementsInterfaceMethods()
    {
        $path = str_replace('test/server/Unit/Models/Base', 'server/Models/Base', __DIR__);
        $classes = $this->loadClasses($path);
        $classInterfaceMap = $this->loadInterfaces($classes);
        if (empty($classInterfaceMap)) {
            self::fail('BaseModelsTest has no classes to test!');
        }
        $this->classImplementsInterfaceMethods($classInterfaceMap);
    }
}
