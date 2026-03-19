<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Repository;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\WysiwygModule\Migration\Service\AltTextMigrationServiceInterface;

class AltTextMigrationRepository implements AltTextMigrationRepositoryInterface
{
    public function __construct(
        private readonly AltTextMigrationServiceInterface $altTextMigrationService,
        private readonly QueryBuilderFactoryInterface $queryBuilderFactory,
    ) {
    }

    public function migrateTableField(string $tableName, string $fieldName, string $tableKey): array
    {
        $selectionQueryBuilder = $this->queryBuilderFactory->create();
        $originalData = $selectionQueryBuilder->select($tableKey, $fieldName)->from($tableName)->execute();

        $updateQueryBuilder = $this->queryBuilderFactory->create();
        $updateQueryBuilder->update($tableName)
            ->set($fieldName, ':newValue')
            ->where($tableKey . ' = :keyValue');

        $allCustomAltTextTags = [];

        while ($originalRow = $originalData->fetchAssociative()) {
            $result = $this->altTextMigrationService->migrateAltTexts($originalRow[$fieldName]);

            $updateQueryBuilder->setParameters([
                ':newValue' => $result->getContent(),
                ':keyValue' => $originalRow[$tableKey],
            ])->execute();

            foreach ($result->getCustomAltTextTags() as $customTag) {
                $allCustomAltTextTags[] = array_merge($customTag, [
                    'key' => $originalRow[$tableKey],
                ]);
            }
        }

        return $allCustomAltTextTags;
    }
}
