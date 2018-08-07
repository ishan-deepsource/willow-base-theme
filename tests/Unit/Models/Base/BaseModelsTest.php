<?php

namespace Bonnier\Willow\Base\Tests\Unit\Models\Base;

use Bonnier\Willow\Base\Tests\Unit\ClassTestCase;

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
