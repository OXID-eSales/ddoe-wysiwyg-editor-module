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
     * Migrates the media references of the given content, reporting every reference it came
     * across, converted or not.
     */
    public function migrateContent(string $content, string $key = ''): ContentMigrationResultInterface;
}
