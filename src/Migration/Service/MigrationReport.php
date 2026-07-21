<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportEntry;

class MigrationReport implements MigrationReportInterface
{
    /** @var MigrationReportEntry[] */
    private array $entries = [];

    private int $rowsScanned = 0;

    private string $table = '';
    private string $field = '';
    private string $oxid = '';

    public function reset(): void
    {
        $this->entries = [];
        $this->rowsScanned = 0;
        $this->table = '';
        $this->field = '';
        $this->oxid = '';
    }

    public function startContext(string $table, string $field, string $key): void
    {
        $this->table = $table;
        $this->field = $field;
        $this->oxid = $key;
        $this->rowsScanned++;
    }

    public function recordConverted(string $attribute, string $reference, string $mediaId, bool $imported): void
    {
        $this->entries[] = new MigrationReportEntry(
            table: $this->table,
            field: $this->field,
            oxid: $this->oxid,
            attribute: $attribute,
            reference: $reference,
            outcome: $imported ? MigrationOutcome::Imported : MigrationOutcome::ConvertedExisting,
            mediaId: $mediaId,
        );
    }

    public function recordFailure(string $attribute, string $reference, string $reason): void
    {
        $this->entries[] = new MigrationReportEntry(
            table: $this->table,
            field: $this->field,
            oxid: $this->oxid,
            attribute: $attribute,
            reference: $reference,
            outcome: MigrationOutcome::Failed,
            detail: $reason,
        );
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function getFailures(): array
    {
        return array_values(array_filter(
            $this->entries,
            static fn(MigrationReportEntry $entry): bool => $entry->getOutcome() === MigrationOutcome::Failed
        ));
    }

    public function getRowsScanned(): int
    {
        return $this->rowsScanned;
    }

    public function countByOutcome(MigrationOutcome $outcome): int
    {
        return count(array_filter(
            $this->entries,
            static fn(MigrationReportEntry $entry): bool => $entry->getOutcome() === $outcome
        ));
    }

    public function shouldWriteFile(): bool
    {
        return $this->countByOutcome(MigrationOutcome::Failed) > 0
            || $this->countByOutcome(MigrationOutcome::Imported) > 0;
    }
}
