<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

/**
 * The immutable result of one migration run, handed to a reporter for output.
 */
interface MigrationReportInterface
{
    public function getTable(): string;

    public function getField(): string;

    public function getTableKey(): string;

    /**
     * @return MigrationReportEntry[]
     */
    public function getEntries(): array;

    /**
     * @return MigrationReportEntry[]
     */
    public function getFailures(): array;

    public function countByOutcome(MigrationOutcome $outcome): int;
}
