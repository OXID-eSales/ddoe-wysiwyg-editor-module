<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Service;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\WysiwygModule\Migration\Service\MigrationReport;
use OxidEsales\WysiwygModule\Migration\Service\MigrationReportCsvWriter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MigrationReportCsvWriterTest extends TestCase
{
    #[Test]
    public function writesHeaderAndOneRowPerEntry(): void
    {
        $report = new MigrationReport();
        $report->startContext('oxcontents', 'OXCONTENT', 'oxstartslot1');
        $report->recordConverted('src', '/out/pictures/ddmedia/a.jpg', 'id-a', true);
        $report->recordFailure('href', '/out/pictures/ddmedia/missing.jpg', 'file not found on disk');

        $path = sys_get_temp_dir() . '/wf-report-' . uniqid() . '.csv';

        $sut = new MigrationReportCsvWriter($this->createStub(ContextInterface::class));

        try {
            $written = $sut->write($report, $path);
            $this->assertSame($path, $written);

            $rows = array_map('str_getcsv', file($path, FILE_IGNORE_NEW_LINES));

            $this->assertSame(
                [
                    'table',
                    'field',
                    'oxid',
                    'attribute',
                    'reference',
                    'outcome',
                    'media_id',
                    'detail'
                ],
                $rows[0]
            );
            $this->assertSame(
                [
                    'oxcontents',
                    'OXCONTENT',
                    'oxstartslot1',
                    'src',
                    '/out/pictures/ddmedia/a.jpg',
                    'imported',
                    'id-a',
                    '',
                ],
                $rows[1]
            );
            $this->assertSame('failed', $rows[2][5]);
            $this->assertSame('file not found on disk', $rows[2][7]);
            $this->assertCount(3, $rows, 'header + two entries');
        } finally {
            @unlink($path);
        }
    }

    #[Test]
    public function defaultPathLivesInTheShopLogDirectory(): void
    {
        $contextStub = $this->createStub(ContextInterface::class);
        $contextStub->method('getLogFilePath')->willReturn('/var/www/source/log/oxideshop.log');

        $sut = new MigrationReportCsvWriter($contextStub);

        $path = $sut->getDefaultPath('oxcontents', 'OXCONTENT');

        $this->assertStringStartsWith('/var/www/source/log/media-migration-oxcontents-OXCONTENT-', $path);
        $this->assertStringEndsWith('.csv', $path);
    }
}
