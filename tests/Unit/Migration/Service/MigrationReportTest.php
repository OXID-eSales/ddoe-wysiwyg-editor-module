<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\Service\MigrationReport;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MigrationReportTest extends TestCase
{
    #[Test]
    public function recordsConvertedAndImportedWithCurrentContext(): void
    {
        $sut = new MigrationReport();
        $sut->startContext('oxcontents', 'OXCONTENT', 'oxstartslot1');

        $sut->recordConverted('src', '/out/pictures/ddmedia/a.jpg', 'id-a', false);
        $sut->recordConverted('href', '/out/pictures/ddmedia/b.jpg', 'id-b', true);

        $this->assertSame(1, $sut->getRowsScanned());
        $this->assertSame(1, $sut->countByOutcome(MigrationOutcome::ConvertedExisting));
        $this->assertSame(1, $sut->countByOutcome(MigrationOutcome::Imported));

        $entries = $sut->getEntries();
        $this->assertSame('oxcontents', $entries[0]->getTable());
        $this->assertSame('OXCONTENT', $entries[0]->getField());
        $this->assertSame('oxstartslot1', $entries[0]->getOxid());
        $this->assertSame('id-a', $entries[0]->getMediaId());
        $this->assertSame(MigrationOutcome::Imported, $entries[1]->getOutcome());
    }

    #[Test]
    public function recordsFailureWithReasonAndExposesItAsFailure(): void
    {
        $sut = new MigrationReport();
        $sut->startContext('oxcontents', 'OXCONTENT', 'wfmigtest01');

        $sut->recordFailure('src', '/out/pictures/ddmedia/missing.jpg', 'file not found on disk');

        $failures = $sut->getFailures();
        $this->assertCount(1, $failures);
        $this->assertSame('file not found on disk', $failures[0]->getDetail());
        $this->assertSame(1, $sut->countByOutcome(MigrationOutcome::Failed));
    }

    public static function shouldWriteFileProvider(): \Generator
    {
        yield 'nothing recorded' => [
            'record' => null,
            'expected' => false,
        ];

        yield 'converted existing only' => [
            'record' => static fn(MigrationReport $report) => $report->recordConverted('src', 'ref', 'id', false),
            'expected' => false,
        ];

        yield 'imported' => [
            'record' => static fn(MigrationReport $report) => $report->recordConverted('src', 'ref', 'id', true),
            'expected' => true,
        ];

        yield 'failed' => [
            'record' => static fn(MigrationReport $report) => $report->recordFailure('src', 'ref', 'reason'),
            'expected' => true,
        ];
    }

    #[Test]
    #[DataProvider('shouldWriteFileProvider')]
    public function shouldWriteFileOnlyWhenThereAreFailuresOrImports(?callable $record, bool $expected): void
    {
        $sut = new MigrationReport();
        $sut->startContext('t', 'f', 'k');

        if ($record !== null) {
            $record($sut);
        }

        $this->assertSame($expected, $sut->shouldWriteFile());
    }

    #[Test]
    public function resetClearsEntriesAndCounters(): void
    {
        $sut = new MigrationReport();
        $sut->startContext('t', 'f', 'k');
        $sut->recordConverted('src', 'ref', 'id', true);

        $sut->reset();

        $this->assertSame(0, $sut->getRowsScanned());
        $this->assertSame([], $sut->getEntries());
        $this->assertFalse($sut->shouldWriteFile());
    }
}
