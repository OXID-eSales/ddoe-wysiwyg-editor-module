<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Repository;

use Doctrine\DBAL\ForwardCompatibility\Result;
use Generator;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\WysiwygModule\Migration\Service\MigrationServiceInterface;

class FieldMigrationRepository implements FieldMigrationRepositoryInterface
{
    public function __construct(
        private readonly MigrationServiceInterface $migrationService,
        private readonly QueryBuilderFactoryInterface $queryBuilderFactory,
    ) {
    }

    public function migrateTableField(string $tableName, string $fieldName, string $tableKey): array
    {
        $entries = [];

        foreach ($this->getOriginalContent($tableName, $fieldName, $tableKey) as $keyValue => $content) {
            $result = $this->migrationService->migrateContent($content, $keyValue);

            if ($result->getContent() !== $content) {
                $this->updateContent($tableName, $fieldName, $tableKey, $keyValue, $result->getContent());
            }

            array_push($entries, ...$result->getReferences());
        }

        return $entries;
    }

    /**
     * @return Generator<string, string> key of the row => content of the field to migrate
     */
    private function getOriginalContent(string $tableName, string $fieldName, string $tableKey): Generator
    {
        $selectionQueryBuilder = $this->queryBuilderFactory->create();

        /** @var Result $originalData */
        $originalData = $selectionQueryBuilder->select($tableKey, $fieldName)->from($tableName)->execute();

        foreach ($originalData->iterateAssociative() as $originalRow) {
            yield (string)$originalRow[$tableKey] => (string)$originalRow[$fieldName];
        }
    }

    private function updateContent(
        string $tableName,
        string $fieldName,
        string $tableKey,
        string $keyValue,
        string $content
    ): void {
        $updateQueryBuilder = $this->queryBuilderFactory->create();
        $updateQueryBuilder->update($tableName)
            ->set($fieldName, ':newValue')
            ->where($tableKey . ' = :keyValue')
            ->setParameters([
                ':newValue' => $content,
                ':keyValue' => $keyValue,
            ])->execute();
    }
}
