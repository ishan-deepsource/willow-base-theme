<?php

namespace Tests\Bonnier\WpVue\Unit\Adapters\Cxense;

use Tests\Bonnier\WpVue\Unit\ClassTestCase;

class CxenseAdaptersTest extends ClassTestCase
{
    public function testCxenseAdaptersImplementsInterfaceMethods()
    {
        $path = str_replace('test/server/Unit/Adapters/Cxense', 'server/Adapters/Cxense', __DIR__);
        $classes = $this->loadClasses($path);
        $classInterfaceMap = $this->loadInterfaces($classes);
        if (empty($classInterfaceMap)) {
            self::fail('CxenseAdaptersTest has no classes to test!');
        }
        $this->classImplementsInterfaceMethods($classInterfaceMap);
    }
}
