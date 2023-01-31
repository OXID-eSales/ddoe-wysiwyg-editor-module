<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Core;

use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\WysiwygModule\Module;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class defines what module does on Shop events.
 */
class Events
{
    /**
     * Execute action on activate event
     */
    public static function onActivate()
    {
        self::executeMigrations();
    }

    private static function executeMigrations(): void
    {
        $migrations = (new MigrationsBuilder())->build();

        $output = new BufferedOutput();
        $migrations->setOutput($output);
        $needsUpdate = $migrations->execute('migrations:up-to-date', Module::MODULE_ID);

        if ($needsUpdate) {
            $migrations->execute('migrations:migrate', Module::MODULE_ID);
        }
    }

    /**
     * Execute action on deactivate event
     */
    public static function onDeactivate()
    {
    }
}
