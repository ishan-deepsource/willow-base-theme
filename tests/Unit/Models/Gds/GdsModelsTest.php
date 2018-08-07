<?php

namespace Bonnier\Willow\Base\Tests\Unit\Models\Gds;

use Bonnier\Willow\Base\Tests\Unit\ClassTestCase;

class GdsModelsTest extends ClassTestCase
{
    public function testModelsImplementsInterfaceMethods()
    {
        $path = str_replace('test/server/Unit/Models/Gds', 'server/Models/Gds', __DIR__);
        $classes = $this->loadClasses($path);
        $classInterfaceMap = $this->loadInterfaces($classes);
        if (empty($classInterfaceMap)) {
            self::fail('GdsModelsTest has no classes to test!');
        }
        $this->classImplementsInterfaceMethods($classInterfaceMap);
    }
}
