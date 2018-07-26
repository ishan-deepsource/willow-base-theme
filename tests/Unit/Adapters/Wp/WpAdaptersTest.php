<?php

namespace Tests\Bonnier\Willow\Base\Unit\Adapters\Wp;

use Tests\Bonnier\Willow\Base\Unit\ClassTestCase;

class WpAdaptersTest extends ClassTestCase
{
    public static function setupBeforeClass()
    {
        parent::setupBeforeClass();
    }

    public function testWpAdaptersImplementsInterfaceMethods()
    {
        $path = str_replace('test/server/Unit/Adapters/Wp', 'server/Adapters/Wp', __DIR__);
        $classes = $this->loadClasses($path);
        $classInterfaceMap = $this->loadInterfaces($classes);
        if (empty($classInterfaceMap)) {
            self::fail('WpAdaptersTest has no classes to test!');
        }
        $this->classImplementsInterfaceMethods($classInterfaceMap);
    }
}
