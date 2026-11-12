<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\TableFieldInterface;
use OxidEsales\WysiwygModule\Migration\Repository\FieldMigrationRepositoryInterface;
use OxidEsales\WysiwygModule\Migration\Service\FieldMigrationService;
use OxidEsales\WysiwygModule\Migration\Service\FieldMigrationServiceInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FieldMigrationServiceTest extends TestCase
{
    #[Test]
    public function migrateRunsTheMigrationOnTheRequestedTableField(): void
    {
        $tableFieldStub = $this->createStub(TableFieldInterface::class);

        $repositoryMock = $this->createMock(FieldMigrationRepositoryInterface::class);
        $repositoryMock->expects($this->once())
            ->method('migrateTableField')
            ->with($tableFieldStub)
            ->willReturn([]);

        $sut = $this->getSut(fieldMigrationRepository: $repositoryMock);

        $sut->migrate($tableFieldStub);
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

        $tableFieldStub = $this->createStub(TableFieldInterface::class);

        $sut = $this->getSut(fieldMigrationRepository: $repositoryStub);

        $report = $sut->migrate($tableFieldStub);

        $this->assertSame($tableFieldStub, $report->getTableField());
        $this->assertSame($entries, $report->getEntries());
    }

    #[Test]
    public function migrateReportsEmptyRunWhenThereIsNothingToMigrate(): void
    {
        $repositoryStub = $this->createConfiguredStub(
            FieldMigrationRepositoryInterface::class,
            ['migrateTableField' => []]
        );

        $sut = $this->getSut(fieldMigrationRepository: $repositoryStub);

        $report = $sut->migrate($this->createStub(TableFieldInterface::class));

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
