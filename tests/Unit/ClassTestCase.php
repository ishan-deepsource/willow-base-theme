<?php

namespace Bonnier\Willow\Base\Tests\Unit;

use Illuminate\Support\Collection;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class ClassTestCase extends TestCase
{
    protected function loadClasses($path): Collection
    {
        $files = self::findFiles($path);
        return collect($files)->reject(function (string $filename) {
            $class = new \ReflectionClass($filename);
            return $class->isInterface() || $class->isAbstract();
        });
    }

    protected function loadInterfaces(Collection $classes): Collection
    {
        return $classes->mapWithKeys(function (string $class) {
            try {
                $interfaces = (new ReflectionClass($class))->getInterfaces();
                return [$class => key($interfaces)];
            } catch (ReflectionException $exception) {
                self::fail($exception->getMessage());
            }
            return null;
        })->reject(function ($classMap) {
            return is_null($classMap);
        });
    }

    protected function loadContracts($path): Collection
    {
        $files = self::findFiles($path);
        return collect($files)->map(function (string $filename) {
            return new ReflectionClass($filename);
        })->reject(function (ReflectionClass $contract) {
            return !$contract->isInterface();
        });
    }

    protected function classImplementsInterfaceMethods(Collection $classInterfaceMap)
    {
        $classInterfaceMap->each(function ($interface, $class) {
            try {
                collect(get_class_methods($interface))->each(function ($method) use ($class) {
                    $message = sprintf(
                        'Class \'%s\' does not implement \'%s\'',
                        $class,
                        $method
                    );
                    $this->assertTrue(method_exists($class, $method), $message);
                });
            } catch (Exception $exception) {
                $this->fail(sprintf('Class \'%s\' is not implementing an interface!', $class));
            }
        });
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
