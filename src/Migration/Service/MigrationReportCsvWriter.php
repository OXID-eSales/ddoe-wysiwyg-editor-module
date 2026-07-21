<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use RuntimeException;

class MigrationReportCsvWriter implements MigrationReportCsvWriterInterface
{
    private const HEADER = ['table', 'field', 'oxid', 'attribute', 'reference', 'outcome', 'media_id', 'detail'];

    public function __construct(
        private readonly ContextInterface $context,
    ) {
    }

    public function write(MigrationReportInterface $report, string $path): string
    {
        $handle = fopen($path, 'w');
        if ($handle === false) {
            throw new RuntimeException("Could not open report file for writing: $path");
        }

        try {
            fputcsv($handle, self::HEADER);
            foreach ($report->getEntries() as $entry) {
                fputcsv($handle, [
                    $entry->getTable(),
                    $entry->getField(),
                    $entry->getOxid(),
                    $entry->getAttribute(),
                    $entry->getReference(),
                    $entry->getOutcome()->value,
                    $entry->getMediaId(),
                    $entry->getDetail(),
                ]);
            }
        } finally {
            fclose($handle);
        }

        return $path;
    }

    public function getDefaultPath(string $table, string $field): string
    {
        $logDirectory = dirname($this->context->getLogFilePath());
        $fileName = sprintf('media-migration-%s-%s-%s.csv', $table, $field, date('Ymd-His'));

        return $logDirectory . '/' . $fileName;
    }
}
