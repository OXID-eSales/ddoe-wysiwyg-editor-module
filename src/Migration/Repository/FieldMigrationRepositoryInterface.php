<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Repository;

use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportEntry;

interface FieldMigrationRepositoryInterface
{
    /**
     * Migrates the media references of one table field and returns every reference
     * the migration came across, located in the row it was found in.
     *
     * @return MigrationReportEntry[]
     */
    public function migrateTableField(string $tableName, string $fieldName, string $tableKey): array;
}
