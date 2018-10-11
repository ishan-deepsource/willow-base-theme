<?php

namespace Bonnier\Willow\Base\Tests\Unit\Models\Contracts;

use Bonnier\Willow\Base\Tests\Unit\ClassTestCase;

class ContractsTest extends ClassTestCase
{
    public function testContractsAreGeneric()
    {
        $returnTypeBlacklist = collect([
            \WP_Post::class,
            \WP_Term::class,
            \WP_User::class,
            \WP_Comment::class,
        ]);
        $path = str_replace('tests/Unit/Models/Contracts', 'src/Models/Contracts', __DIR__);
        $contracts = $this->loadContracts($path);
        if ($contracts->isEmpty()) {
            self::fail('ContractsTest has no classes to test!');
        }
        $contracts->each(function (\ReflectionClass $contract) use ($returnTypeBlacklist) {
            collect($contract->getMethods())
                ->each(function (\ReflectionMethod $method) use ($contract, $returnTypeBlacklist) {
                    $message = sprintf(
                        'Contract \'%s\' has a method (%s) that returns a WordPress specific type',
                        $contract->getName(),
                        $method->getName()
                    );
                    $this->assertFalse(
                        $returnTypeBlacklist->contains($method->getReturnType()->getName()),
                        $message
                    );
                });
        });
    }
}
