<?php

namespace Bonnier\Willow\Base\Tests\Unit\Models\Bob;

use Bonnier\Willow\Base\Tests\Unit\ClassTestCase;

class BobModelsTest extends ClassTestCase
{
    public function testModelsImplementsInterfaceMethods()
    {
        $path = str_replace('tests/Unit/Models/Bob', 'src/Models/Bob', __DIR__);
        $classes = $this->loadClasses($path);
        $classInterfaceMap = $this->loadInterfaces($classes);
        if ($classInterfaceMap->isEmpty()) {
            self::fail('BobModelsTest has no classes to test!');
        }
        $this->classImplementsInterfaceMethods($classInterfaceMap);
    }
}
