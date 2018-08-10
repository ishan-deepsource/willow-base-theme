<?php

namespace Bonnier\Willow\Base\Tests\Units\Adapters\Wp;

use Bonnier\Willow\Base\Tests\Units\ClassTestCase;

class WpAdaptersTest extends ClassTestCase
{
    public static function setupBeforeClass()
    {
        parent::setupBeforeClass();
    }

    public function testWpAdaptersImplementsInterfaceMethods()
    {
        $path = str_replace('tests/unit/Adapters/Wp', 'src/Adapters/Wp', __DIR__);
        $classes = $this->loadClasses($path);
        $classInterfaceMap = $this->loadInterfaces($classes);
        if (empty($classInterfaceMap)) {
            self::fail('WpAdaptersTest has no classes to test!');
        }
        $this->classImplementsInterfaceMethods($classInterfaceMap);
    }
}
