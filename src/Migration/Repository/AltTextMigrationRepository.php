<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Repository;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\WysiwygModule\Migration\DTO\TableFieldInterface;
use OxidEsales\WysiwygModule\Migration\Service\AltTextMigrationServiceInterface;

class AltTextMigrationRepository implements AltTextMigrationRepositoryInterface
{
    public function __construct(
        private readonly AltTextMigrationServiceInterface $altTextMigrationService,
        private readonly QueryBuilderFactoryInterface $queryBuilderFactory,
    ) {
    }

    public function migrateTableField(TableFieldInterface $tableField): array
    {
        $tableName = $tableField->getTable();
        $fieldName = $tableField->getField();
        $keyField = $tableField->getKeyField();

        $selectionQueryBuilder = $this->queryBuilderFactory->create();
        $originalData = $selectionQueryBuilder->select($keyField, $fieldName)->from($tableName)->execute();

        $updateQueryBuilder = $this->queryBuilderFactory->create();
        $updateQueryBuilder->update($tableName)
            ->set($fieldName, ':newValue')
            ->where($keyField . ' = :keyValue');

        $allCustomAltTextTags = [];

        while ($originalRow = $originalData->fetchAssociative()) {
            $keyValue = (string)$originalRow[$keyField];
            $result = $this->altTextMigrationService->migrateAltTexts((string)$originalRow[$fieldName], $keyValue);

            $updateQueryBuilder->setParameters([
                ':newValue' => $result->getContent(),
                ':keyValue' => $keyValue,
            ])->execute();

            array_push($allCustomAltTextTags, ...$result->getCustomAltTextTags());
        }

        return $allCustomAltTextTags;
    }
}
