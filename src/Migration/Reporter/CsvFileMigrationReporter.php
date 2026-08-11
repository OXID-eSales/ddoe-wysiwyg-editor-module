<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Reporter;

use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

class CsvFileMigrationReporter implements MigrationReporterInterface
{
    private const HEADER = ['table', 'field', 'key', 'attribute', 'path', 'outcome', 'media_id', 'detail'];

    public function __construct(
        private readonly string $path,
    ) {
    }

    public function report(MigrationReportInterface $report, OutputInterface $output): void
    {
        $handle = @fopen($this->path, 'w');

        if ($handle === false) {
            throw new RuntimeException("Could not open report file for writing: $this->path");
        }

        try {
            fputcsv($handle, self::HEADER);

            foreach ($report->getEntries() as $entry) {
                fputcsv($handle, [
                    $report->getTable(),
                    $report->getField(),
                    $entry->getKey(),
                    $entry->getReference()->getAttribute(),
                    $entry->getReference()->getPath(),
                    $entry->getReference()->getOutcome()->value,
                    $entry->getReference()->getMediaId(),
                    $entry->getReference()->getDetail(),
                ]);
            }
        } finally {
            fclose($handle);
        }

        $output->writeln(sprintf(
            'Report of %d media references (%d failed) written to %s',
            count($report->getEntries()),
            count($report->getFailures()),
            $this->path
        ));
    }
}
