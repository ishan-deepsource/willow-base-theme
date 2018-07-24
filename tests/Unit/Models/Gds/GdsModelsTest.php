<?php

namespace Tests\Bonnier\WpVue\Unit\Models\Gds;

use Tests\Bonnier\WpVue\Unit\ClassTestCase;
use Bonnier\WpVue\Models\Gds\Composite;

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
