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
     * Every entry of the report, or only the ones with the given outcome.
     *
     * @return MediaMigrationResultInterface[]
     */
    public function getEntries(?MigrationOutcome $outcome = null): array;
}
