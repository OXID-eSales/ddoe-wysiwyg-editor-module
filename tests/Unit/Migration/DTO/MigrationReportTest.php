<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\DTO;

use OxidEsales\WysiwygModule\Migration\DTO\MediaReferenceResult;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReport;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportEntry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MigrationReportTest extends TestCase
{
    #[Test]
    public function reportExposesWhatTheRunWasAbout(): void
    {
        $sut = new MigrationReport(
            table: 'oxcontents',
            field: 'OXCONTENT',
            tableKey: 'OXID',
            entries: [],
        );

        $this->assertSame('oxcontents', $sut->getTable());
        $this->assertSame('OXCONTENT', $sut->getField());
        $this->assertSame('OXID', $sut->getTableKey());
        $this->assertSame([], $sut->getEntries());
    }

    #[Test]
    public function countByOutcomeCountsOnlyMatchingEntries(): void
    {
        $sut = $this->getSut([
            $this->makeEntry(MigrationOutcome::Converted),
            $this->makeEntry(MigrationOutcome::Converted),
            $this->makeEntry(MigrationOutcome::Failed),
        ]);

        $this->assertSame(2, $sut->countByOutcome(MigrationOutcome::Converted));
        $this->assertSame(1, $sut->countByOutcome(MigrationOutcome::Failed));
    }

    #[Test]
    public function getFailuresReturnsFailedEntriesReindexed(): void
    {
        $failedEntry = $this->makeEntry(MigrationOutcome::Failed, 'file not found');

        $sut = $this->getSut([
            $this->makeEntry(MigrationOutcome::Converted),
            $failedEntry,
        ]);

        $failures = $sut->getFailures();

        $this->assertSame([$failedEntry], $failures);
        $this->assertSame('file not found', $failures[0]->getReference()->getDetail());
    }

    #[Test]
    public function getFailuresIsEmptyWhenEverythingWasConverted(): void
    {
        $sut = $this->getSut([$this->makeEntry(MigrationOutcome::Converted)]);

        $this->assertSame([], $sut->getFailures());
    }

    /**
     * @param MigrationReportEntry[] $entries
     */
    private function getSut(array $entries): MigrationReport
    {
        return new MigrationReport(
            table: 'oxcontents',
            field: 'OXCONTENT',
            tableKey: 'OXID',
            entries: $entries,
        );
    }

    private function makeEntry(MigrationOutcome $outcome, string $detail = ''): MigrationReportEntry
    {
        return new MigrationReportEntry(
            key: uniqid(),
            reference: new MediaReferenceResult(
                attribute: 'src',
                path: '/out/pictures/ddmedia/' . uniqid() . '.jpg',
                outcome: $outcome,
                detail: $detail,
            ),
        );
    }
}
