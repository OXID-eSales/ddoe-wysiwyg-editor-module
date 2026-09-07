<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\MigrationReport;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use OxidEsales\WysiwygModule\Migration\Repository\FieldMigrationRepositoryInterface;

class FieldMigrationService implements FieldMigrationServiceInterface
{
    public function __construct(
        private readonly FieldMigrationRepositoryInterface $fieldMigrationRepository,
    ) {
    }

    public function migrate(string $tableName, string $fieldName, string $tableKey): MigrationReportInterface
    {
        return new MigrationReport(
            table: $tableName,
            field: $fieldName,
            tableKey: $tableKey,
            entries: $this->fieldMigrationRepository->migrateTableField($tableName, $fieldName, $tableKey),
        );
    }
}
