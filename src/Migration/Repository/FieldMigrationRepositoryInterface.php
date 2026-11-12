<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\Repository;

use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\TableFieldInterface;

interface FieldMigrationRepositoryInterface
{
    /**
     * Migrates the media references of one table field and returns every reference
     * the migration came across, each identified by the row it was found in.
     *
     * @return MediaMigrationResultInterface[]
     */
    public function migrateTableField(TableFieldInterface $tableField): array;
}
