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
     * @param MigrationReportEntry[] $entries
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

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function getFailures(): array
    {
        return $this->filterByOutcome(MigrationOutcome::Failed);
    }

    public function countByOutcome(MigrationOutcome $outcome): int
    {
        return count($this->filterByOutcome($outcome));
    }

    /**
     * @return MigrationReportEntry[]
     */
    private function filterByOutcome(MigrationOutcome $outcome): array
    {
        return array_values(array_filter(
            $this->entries,
            static fn(MigrationReportEntry $entry): bool => $entry->getReference()->getOutcome() === $outcome
        ));
    }
}
