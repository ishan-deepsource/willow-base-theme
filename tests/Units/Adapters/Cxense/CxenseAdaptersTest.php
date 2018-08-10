<?php

namespace Bonnier\Willow\Base\Tests\Units\Adapters\Cxense;

use Bonnier\Willow\Base\Tests\Units\ClassTestCase;

class CxenseAdaptersTest extends ClassTestCase
{
    public function testCxenseAdaptersImplementsInterfaceMethods()
    {
        $path = str_replace('tests/unit/Adapters/Cxense', 'src/Adapters/Cxense', __DIR__);
        $classes = $this->loadClasses($path);
        $classInterfaceMap = $this->loadInterfaces($classes);
        if (empty($classInterfaceMap)) {
            self::fail('CxenseAdaptersTest has no classes to test!');
        }
        $this->classImplementsInterfaceMethods($classInterfaceMap);
    }
}
