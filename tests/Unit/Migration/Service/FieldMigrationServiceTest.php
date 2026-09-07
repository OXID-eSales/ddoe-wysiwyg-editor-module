<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\Repository\FieldMigrationRepositoryInterface;
use OxidEsales\WysiwygModule\Migration\Service\FieldMigrationService;
use OxidEsales\WysiwygModule\Migration\Service\FieldMigrationServiceInterface;
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

        $sut = $this->getSut($repositorySpy);

        $sut->migrate(self::TABLE, self::FIELD, self::TABLE_KEY);
    }

    #[Test]
    public function migrateReturnsReportDescribingTheRun(): void
    {
        $entries = [
            $this->createStub(MediaMigrationResultInterface::class),
            $this->createStub(MediaMigrationResultInterface::class),
        ];

        $repositoryStub = $this->createConfiguredStub(
            FieldMigrationRepositoryInterface::class,
            ['migrateTableField' => $entries]
        );

        $sut = $this->getSut($repositoryStub);

        $report = $sut->migrate(self::TABLE, self::FIELD, self::TABLE_KEY);

        $this->assertSame(self::TABLE, $report->getTable());
        $this->assertSame(self::FIELD, $report->getField());
        $this->assertSame(self::TABLE_KEY, $report->getTableKey());
        $this->assertSame($entries, $report->getEntries());
    }

    #[Test]
    public function migrateReportsEmptyRunWhenThereIsNothingToMigrate(): void
    {
        $repositoryStub = $this->createConfiguredStub(
            FieldMigrationRepositoryInterface::class,
            ['migrateTableField' => []]
        );

        $sut = $this->getSut($repositoryStub);

        $report = $sut->migrate(self::TABLE, self::FIELD, self::TABLE_KEY);

        $this->assertSame([], $report->getEntries());
    }

    private function getSut(
        ?FieldMigrationRepositoryInterface $fieldMigrationRepository = null
    ): FieldMigrationServiceInterface {
        $fieldMigrationRepository ??= $this->createStub(FieldMigrationRepositoryInterface::class);

        return new FieldMigrationService(
            fieldMigrationRepository: $fieldMigrationRepository,
        );
    }
}
