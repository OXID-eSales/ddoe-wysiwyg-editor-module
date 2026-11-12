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
use OxidEsales\WysiwygModule\Migration\DTO\TableFieldInterface;
use OxidEsales\WysiwygModule\Migration\Service\MigrationServiceInterface;

class FieldMigrationRepository implements FieldMigrationRepositoryInterface
{
    public function __construct(
        private readonly MigrationServiceInterface $migrationService,
        private readonly QueryBuilderFactoryInterface $queryBuilderFactory,
    ) {
    }

    public function migrateTableField(TableFieldInterface $tableField): array
    {
        $entries = [];

        foreach ($this->getOriginalContent($tableField) as $keyValue => $content) {
            $result = $this->migrationService->migrateContent($content, $keyValue);

            if ($result->getContent() !== $content) {
                $this->updateContent($tableField, $keyValue, $result->getContent());
            }

            array_push($entries, ...$result->getReferences());
        }

        return $entries;
    }

    /**
     * @return Generator<string, string> key of the row => content of the field to migrate
     */
    private function getOriginalContent(TableFieldInterface $tableField): Generator
    {
        $fieldName = $tableField->getField();
        $keyField = $tableField->getKeyField();

        $selectionQueryBuilder = $this->queryBuilderFactory->create();

        /** @var Result $originalData */
        $originalData = $selectionQueryBuilder
            ->select($keyField, $fieldName)
            ->from($tableField->getTable())
            ->execute();

        foreach ($originalData->iterateAssociative() as $originalRow) {
            yield (string)$originalRow[$keyField] => (string)$originalRow[$fieldName];
        }
    }

    private function updateContent(TableFieldInterface $tableField, string $keyValue, string $content): void
    {
        $updateQueryBuilder = $this->queryBuilderFactory->create();
        $updateQueryBuilder->update($tableField->getTable())
            ->set($tableField->getField(), ':newValue')
            ->where($tableField->getKeyField() . ' = :keyValue')
            ->setParameters([
                ':newValue' => $content,
                ':keyValue' => $keyValue,
            ])->execute();
    }
}
