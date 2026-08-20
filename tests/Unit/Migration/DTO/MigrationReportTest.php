<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\DTO;

use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReport;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MigrationReportTest extends TestCase
{
    private const TABLE = 'oxcontents';
    private const FIELD = 'OXCONTENT';
    private const TABLE_KEY = 'OXID';

    #[Test]
    public function reportExposesWhatTheRunWasAbout(): void
    {
        $sut = $this->getSut([]);

        $this->assertSame(self::TABLE, $sut->getTable());
        $this->assertSame(self::FIELD, $sut->getField());
        $this->assertSame(self::TABLE_KEY, $sut->getTableKey());
        $this->assertSame([], $sut->getEntries());
    }

    #[Test]
    public function getEntriesReturnsEveryEntryInTheGivenOrder(): void
    {
        $entries = [
            $this->makeEntryStub(MigrationOutcome::Converted),
            $this->makeEntryStub(MigrationOutcome::Failed),
        ];

        $sut = $this->getSut($entries);

        $this->assertSame($entries, $sut->getEntries());
    }

    #[Test]
    public function getEntriesWithAnOutcomeReturnsOnlyMatchingEntriesReindexed(): void
    {
        $failedEntry = $this->makeEntryStub(MigrationOutcome::Failed);

        $sut = $this->getSut([
            $this->makeEntryStub(MigrationOutcome::Converted),
            $failedEntry,
            $this->makeEntryStub(MigrationOutcome::Converted),
        ]);

        $this->assertSame([$failedEntry], $sut->getEntries(MigrationOutcome::Failed));
        $this->assertCount(2, $sut->getEntries(MigrationOutcome::Converted));
    }

    #[Test]
    public function getEntriesWithAnOutcomeIsEmptyWhenNoEntryMatches(): void
    {
        $sut = $this->getSut([$this->makeEntryStub(MigrationOutcome::Converted)]);

        $this->assertSame([], $sut->getEntries(MigrationOutcome::Failed));
    }

    /**
     * @param MediaMigrationResultInterface[] $entries
     */
    private function getSut(array $entries): MigrationReportInterface
    {
        return new MigrationReport(
            table: self::TABLE,
            field: self::FIELD,
            tableKey: self::TABLE_KEY,
            entries: $entries,
        );
    }

    private function makeEntryStub(MigrationOutcome $outcome): MediaMigrationResultInterface
    {
        $entryStub = $this->createStub(MediaMigrationResultInterface::class);
        $entryStub->method('getOutcome')->willReturn($outcome);

        return $entryStub;
    }
}
