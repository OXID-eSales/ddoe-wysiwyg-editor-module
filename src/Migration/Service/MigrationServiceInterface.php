<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\ContentMigrationResultInterface;

interface MigrationServiceInterface
{
    /**
     * Migrates the media references of the given content and returns the migrated content.
     */
    public function migrateContent(string $content): string;

    /**
     * The same migration, additionally reporting every media reference it came across,
     * converted or not. Used to build the migration report.
     */
    public function migrateContentWithReferences(string $content): ContentMigrationResultInterface;
}
