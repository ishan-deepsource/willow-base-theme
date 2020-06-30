<?php

namespace Bonnier\Willow\Base\Database\Migrations;

use Bonnier\Willow\Base\Exceptions\Database\MigrationException;

class Migrate
{
    const OPTION = 'bonnier-willow-base-migrations';
    const NOT_FOUND_TABLE = 'bonnier_not_found';

    public static function run()
    {
        $dbVersion = intval(get_option(self::OPTION) ?: 0);
        $migrations = collect([
            CreateFeatureDatesTable::class,
            CreateNotFoundTable::class,
            AlterNotFoundTableAddIgnoreEntry::class,
        ]);

        if ($dbVersion >= count($migrations)) {
            return;
        }

        $migrations->each(function (string $migration, int $index) use ($dbVersion) {
            $migrationReflection = new \ReflectionClass($migration);
            if (!$migrationReflection->implementsInterface(Migration::class)) {
                throw new MigrationException(
                    sprintf('The migration \'%s\' does not implement the Migration interface', $migration)
                );
            }
            if ($index < $dbVersion) {
                return;
            }
            /** @var Migration $migration */
            $migration::migrate();
            if ($migration::verify()) {
                update_option(self::OPTION, $index + 1);
            } else {
                throw new MigrationException(sprintf('An error occured running the migration \'%s\'', $migration));
            }
        });
    }
}
