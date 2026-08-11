<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Reporter;

use OxidEsales\WysiwygModule\Migration\DTO\MediaReferenceResult;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReport;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportEntry;
use OxidEsales\WysiwygModule\Migration\Reporter\CsvFileMigrationReporter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Output\BufferedOutput;

class CsvFileMigrationReporterTest extends TestCase
{
    private string $path = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->path = sys_get_temp_dir() . '/wysiwyg-media-migration-' . uniqid() . '.csv';
    }

    protected function tearDown(): void
    {
        @unlink($this->path);
        parent::tearDown();
    }

    #[Test]
    public function reportWritesHeaderAndOneRowPerEntry(): void
    {
        $sut = new CsvFileMigrationReporter($this->path);
        $sut->report($this->getReport(), new BufferedOutput());

        $rows = array_map('str_getcsv', file($this->path, FILE_IGNORE_NEW_LINES));

        $this->assertCount(3, $rows, 'header + two entries');
        $this->assertSame(
            ['table', 'field', 'key', 'attribute', 'path', 'outcome', 'media_id', 'detail'],
            $rows[0]
        );
        $this->assertSame(
            [
                'oxcontents',
                'OXCONTENT',
                'oxstartslot1',
                'src',
                '/out/pictures/ddmedia/a.jpg',
                'converted',
                'id-a',
                '',
            ],
            $rows[1]
        );
        $this->assertSame(
            [
                'oxcontents',
                'OXCONTENT',
                'oxstartslot2',
                'href',
                '/out/pictures/ddmedia/missing.jpg',
                'failed',
                '',
                'media not registered',
            ],
            $rows[2]
        );
    }

    #[Test]
    public function reportTellsWhereTheReportWasWritten(): void
    {
        $output = new BufferedOutput();

        $sut = new CsvFileMigrationReporter($this->path);
        $sut->report($this->getReport(), $output);

        $display = $output->fetch();

        $this->assertStringContainsString($this->path, $display);
        $this->assertStringContainsString('2 media references (1 failed)', $display);
    }

    #[Test]
    public function reportThrowsWhenTheFileCannotBeWritten(): void
    {
        $sut = new CsvFileMigrationReporter('/does/not/exist/' . uniqid() . '.csv');

        $this->expectException(RuntimeException::class);
        $sut->report($this->getReport(), new BufferedOutput());
    }

    private function getReport(): MigrationReport
    {
        return new MigrationReport(
            table: 'oxcontents',
            field: 'OXCONTENT',
            tableKey: 'OXID',
            entries: [
                new MigrationReportEntry(
                    key: 'oxstartslot1',
                    reference: new MediaReferenceResult(
                        attribute: 'src',
                        path: '/out/pictures/ddmedia/a.jpg',
                        outcome: MigrationOutcome::Converted,
                        mediaId: 'id-a',
                    ),
                ),
                new MigrationReportEntry(
                    key: 'oxstartslot2',
                    reference: new MediaReferenceResult(
                        attribute: 'href',
                        path: '/out/pictures/ddmedia/missing.jpg',
                        outcome: MigrationOutcome::Failed,
                        detail: 'media not registered',
                    ),
                ),
            ],
        );
    }
}
