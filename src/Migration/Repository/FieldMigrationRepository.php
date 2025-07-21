<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Repository;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\WysiwygModule\Migration\Service\MigrationServiceInterface;

class FieldMigrationRepository implements FieldMigrationRepositoryInterface
{
    public function __construct(
        private readonly MigrationServiceInterface $migrationService,
        private readonly QueryBuilderFactoryInterface $queryBuilderFactory,
    ) {
    }

    public function migrateTableField(string $tableName, string $fieldName, string $tableKey): void
    {
        $selectionQueryBuilder = $this->queryBuilderFactory->create();
        $originalData = $selectionQueryBuilder->select($tableKey, $fieldName)->from($tableName)->execute();

        $updateQueryBuilder = $this->queryBuilderFactory->create();
        $updateQueryBuilder->update($tableName)
            ->set($fieldName, ':newValue')
            ->where($tableKey . ' = :keyValue');

        while ($originalRow = $originalData->fetchAssociative()) {
            $updateQueryBuilder->setParameters([
                ':newValue' => $this->migrationService->migrateContent($originalRow[$fieldName]),
                ':keyValue' => $originalRow[$tableKey],
            ])->execute();
        }
    }
}