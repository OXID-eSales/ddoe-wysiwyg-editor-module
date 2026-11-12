<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Reporter;

use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationSummary;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationSummaryInterface;
use OxidEsales\WysiwygModule\Migration\Service\MediaMigrationResultFilterInterface;
use RuntimeException;

class CsvFileMigrationReporter implements MigrationReporterInterface
{
    private const HEADER = ['table', 'field', 'key', 'attribute', 'path', 'outcome', 'media_id', 'detail'];

    private const CSV_ESCAPE = '';

    public function __construct(
        private readonly MediaMigrationResultFilterInterface $resultFilter,
        private readonly string $path,
    ) {
    }

    public function report(MigrationReportInterface $report): MigrationSummaryInterface
    {
        $handle = @fopen($this->path, 'w');

        if ($handle === false) {
            throw new RuntimeException("Could not open report file for writing: $this->path");
        }

        try {
            fputcsv($handle, self::HEADER, escape: self::CSV_ESCAPE);

            $tableField = $report->getTableField();
            foreach ($report->getEntries() as $entry) {
                fputcsv($handle, [
                    $tableField->getTable(),
                    $tableField->getField(),
                    $entry->getKey(),
                    $entry->getAttribute(),
                    $entry->getPath(),
                    $entry->getOutcome()->value,
                    $entry->getMediaId(),
                    $entry->getDetail(),
                ], escape: self::CSV_ESCAPE);
            }
        } finally {
            fclose($handle);
        }

        $failures = $this->resultFilter->filterByOutcome($report->getEntries(), MigrationOutcome::Failed);

        return new MigrationSummary(
            lines: [
                sprintf(
                    'Report of %d media references (%d failed) written to %s',
                    count($report->getEntries()),
                    count($failures),
                    $this->path
                ),
            ],
        );
    }
}
