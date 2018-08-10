<?php

namespace Bonnier\Willow\Base\Tests\Units;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class ClassTestCase extends TestCase
{
    protected function loadClasses($path)
    {
        $files = self::findFiles($path);
        $adapters = collect($files)->reject(function (string $filename) {
            $class = new \ReflectionClass($filename);
            return $class->isInterface() || $class->isAbstract();
        });
        return $adapters->toArray();
    }

    protected function loadInterfaces(array $classes)
    {
        $classInterfaceMap = [];
        foreach ($classes as $class) {
            try {
                $interfaces = (new ReflectionClass($class))->getInterfaces();
                $classInterfaceMap[$class] = key($interfaces);
            } catch (ReflectionException $e) {
                self::fail($e->getMessage());
            }
        }

        return $classInterfaceMap;
    }

    protected function classImplementsInterfaceMethods($classInterfaceMap)
    {
        foreach ($classInterfaceMap as $class => $interface) {
            foreach (get_class_methods($interface) as $method) {
                $message = sprintf('Class %s does not implement %s', $class, $method);
                $this->assertTrue(method_exists($class, $method), $message);
            }
        }
    }

    private function findFiles($path, $files = [])
    {
        $dir = new \DirectoryIterator($path);
        foreach ($dir as $file) {
            if (in_array($file->getFilename(), ['.', '..'])) {
                continue;
            }
            if ($file->getType() === 'dir') {
                $files = array_merge($files, self::findFiles($file->getRealPath()));
            }
            if ($file->getType() === 'file') {
                $src = file_get_contents($file->getRealPath());
                if (preg_match('/namespace (.*);/', $src, $matches)) {
                    array_push(
                        $files,
                        $matches[1] . '\\' . str_replace('.php', '', $file->getFilename())
                    );
                }
            }
        }

        return $files;
    }
}
