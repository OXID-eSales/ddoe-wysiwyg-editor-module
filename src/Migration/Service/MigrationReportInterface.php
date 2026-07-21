<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportEntry;

interface MigrationReportInterface
{
    public function reset(): void;

    /**
     * Sets the row currently being processed. Subsequent recorded entries are attributed to it,
     * and the scanned-rows counter is incremented.
     */
    public function startContext(string $table, string $field, string $key): void;

    public function recordConverted(string $attribute, string $reference, string $mediaId, bool $imported): void;

    public function recordFailure(string $attribute, string $reference, string $reason): void;

    /**
     * @return MigrationReportEntry[]
     */
    public function getEntries(): array;

    /**
     * @return MigrationReportEntry[]
     */
    public function getFailures(): array;

    public function getRowsScanned(): int;

    public function countByOutcome(MigrationOutcome $outcome): int;

    /**
     * Whether the run produced anything worth persisting to a file:
     * at least one failure or one freshly imported media.
     */
    public function shouldWriteFile(): bool;
}
