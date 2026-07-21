<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Service;

interface MigrationReportCsvWriterInterface
{
    /**
     * Writes the report as a flat CSV to the given path and returns the path written.
     */
    public function write(MigrationReportInterface $report, string $path): string;

    /**
     * Builds the default report path for a table/field run inside the shop log directory.
     */
    public function getDefaultPath(string $table, string $field): string;
}
