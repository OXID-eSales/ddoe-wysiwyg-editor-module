<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

class MigrationReport implements MigrationReportInterface
{
    /**
     * @param MediaMigrationResultInterface[] $entries
     */
    public function __construct(
        private readonly string $table,
        private readonly string $field,
        private readonly string $tableKey,
        private readonly array $entries,
    ) {
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getTableKey(): string
    {
        return $this->tableKey;
    }

    public function getEntries(?MigrationOutcome $outcome = null): array
    {
        if ($outcome === null) {
            return $this->entries;
        }

        return array_values(array_filter(
            $this->entries,
            static fn(MediaMigrationResultInterface $entry): bool => $entry->getOutcome() === $outcome
        ));
    }
}
