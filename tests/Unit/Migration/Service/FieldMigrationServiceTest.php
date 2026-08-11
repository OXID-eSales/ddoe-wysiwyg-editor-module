<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\MediaReferenceResult;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportEntry;
use OxidEsales\WysiwygModule\Migration\Repository\FieldMigrationRepositoryInterface;
use OxidEsales\WysiwygModule\Migration\Service\FieldMigrationService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FieldMigrationServiceTest extends TestCase
{
    private const TABLE = 'oxcontents';
    private const FIELD = 'OXCONTENT';
    private const TABLE_KEY = 'OXID';

    #[Test]
    public function migrateRunsTheMigrationOnTheRequestedTableField(): void
    {
        $repositorySpy = $this->createMock(FieldMigrationRepositoryInterface::class);
        $repositorySpy->expects($this->once())
            ->method('migrateTableField')
            ->with(self::TABLE, self::FIELD, self::TABLE_KEY)
            ->willReturn([]);

        $sut = new FieldMigrationService($repositorySpy);

        $sut->migrate(self::TABLE, self::FIELD, self::TABLE_KEY);
    }

    #[Test]
    public function migrateReturnsReportDescribingTheRun(): void
    {
        $entries = [$this->makeEntry(MigrationOutcome::Converted), $this->makeEntry(MigrationOutcome::Failed)];

        $repositoryStub = $this->createStub(FieldMigrationRepositoryInterface::class);
        $repositoryStub->method('migrateTableField')->willReturn($entries);

        $sut = new FieldMigrationService($repositoryStub);

        $report = $sut->migrate(self::TABLE, self::FIELD, self::TABLE_KEY);

        $this->assertSame(self::TABLE, $report->getTable());
        $this->assertSame(self::FIELD, $report->getField());
        $this->assertSame(self::TABLE_KEY, $report->getTableKey());
        $this->assertSame($entries, $report->getEntries());
        $this->assertCount(1, $report->getFailures());
    }

    #[Test]
    public function migrateReportsEmptyRunWhenThereIsNothingToMigrate(): void
    {
        $repositoryStub = $this->createStub(FieldMigrationRepositoryInterface::class);
        $repositoryStub->method('migrateTableField')->willReturn([]);

        $sut = new FieldMigrationService($repositoryStub);

        $report = $sut->migrate(self::TABLE, self::FIELD, self::TABLE_KEY);

        $this->assertSame([], $report->getEntries());
        $this->assertSame([], $report->getFailures());
    }

    private function makeEntry(MigrationOutcome $outcome): MigrationReportEntry
    {
        return new MigrationReportEntry(
            key: uniqid(),
            reference: new MediaReferenceResult(
                attribute: 'src',
                path: '/out/pictures/ddmedia/' . uniqid() . '.jpg',
                outcome: $outcome,
            ),
        );
    }
}
