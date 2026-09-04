<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Integration\Migration\Command;

use OxidEsales\WysiwygModule\Migration\Command\MigrateMediaUrlsToIdsCommand;
use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use OxidEsales\WysiwygModule\Migration\Factory\MigrationReporterFactoryInterface;
use OxidEsales\WysiwygModule\Migration\Reporter\MigrationReporterInterface;
use OxidEsales\WysiwygModule\Migration\Service\FieldMigrationServiceInterface;
use OxidEsales\WysiwygModule\Migration\Service\MediaMigrationResultFilterInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

class MigrateMediaUrlsToIdsCommandTest extends TestCase
{
    #[Test]
    public function migrationCallsServiceWithCorrectParams(): void
    {
        $table = uniqid();
        $field = uniqid();
        $tableKey = uniqid();

        $serviceSpy = $this->createMock(FieldMigrationServiceInterface::class);
        $serviceSpy->expects($this->once())
            ->method('migrate')
            ->with($table, $field, $tableKey)
            ->willReturn($this->createStub(MigrationReportInterface::class));

        $sut = $this->getSut(
            fieldMigrationService: $serviceSpy
        );
        $commandTester = new CommandTester($sut);
        $commandTester->execute(['table' => $table, 'field' => $field, 'tableKey' => $tableKey]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    #[Test]
    public function migrationCallsServiceWithDefaultTableKey(): void
    {
        $table = uniqid();
        $field = uniqid();

        $serviceSpy = $this->createMock(FieldMigrationServiceInterface::class);
        $serviceSpy->expects($this->once())
            ->method('migrate')
            ->with($table, $field, 'OXID')
            ->willReturn($this->createStub(MigrationReportInterface::class));

        $sut = $this->getSut(
            fieldMigrationService: $serviceSpy
        );
        $commandTester = new CommandTester($sut);
        $commandTester->execute(['table' => $table, 'field' => $field]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    #[Test]
    public function migrationHandsTheReportToTheReporter(): void
    {
        $reportStub = $this->createStub(MigrationReportInterface::class);

        $reporterSpy = $this->createMock(MigrationReporterInterface::class);
        $reporterSpy->expects($this->once())
            ->method('report')
            ->with($reportStub, $this->isInstanceOf(OutputInterface::class));

        $sut = $this->getSut(
            fieldMigrationService: $this->createConfiguredStub(
                FieldMigrationServiceInterface::class,
                ['migrate' => $reportStub]
            ),
            reporterFactory: $this->createConfiguredStub(
                MigrationReporterFactoryInterface::class,
                ['create' => $reporterSpy]
            ),
        );

        $commandTester = new CommandTester($sut);
        $commandTester->execute(['table' => uniqid(), 'field' => uniqid()]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    #[Test]
    public function reporterIsBuiltForTheScreenWhenNoReportFileIsRequested(): void
    {
        $factorySpy = $this->createMock(MigrationReporterFactoryInterface::class);
        $factorySpy->expects($this->once())
            ->method('create')
            ->with(null)
            ->willReturn($this->createStub(MigrationReporterInterface::class));

        $sut = $this->getSut(
            reporterFactory: $factorySpy
        );
        $commandTester = new CommandTester($sut);
        $commandTester->execute(['table' => uniqid(), 'field' => uniqid()]);
    }

    #[Test]
    public function reporterIsBuiltForTheRequestedReportFile(): void
    {
        $reportFilePath = uniqid() . '.csv';

        $factorySpy = $this->createMock(MigrationReporterFactoryInterface::class);
        $factorySpy->expects($this->once())
            ->method('create')
            ->with($reportFilePath)
            ->willReturn($this->createStub(MigrationReporterInterface::class));

        $sut = $this->getSut(
            reporterFactory: $factorySpy
        );
        $commandTester = new CommandTester($sut);
        $commandTester->execute([
            'table' => uniqid(),
            'field' => uniqid(),
            '--report-file' => $reportFilePath,
        ]);
    }

    #[Test]
    public function migrationFailsWhenReferencesCouldNotBeConverted(): void
    {
        $filterStub = $this->createConfiguredStub(
            MediaMigrationResultFilterInterface::class,
            ['filterByOutcome' => [$this->createStub(MediaMigrationResultInterface::class)]]
        );

        $sut = $this->getSut(
            resultFilter: $filterStub
        );

        $commandTester = new CommandTester($sut);
        $commandTester->execute(['table' => uniqid(), 'field' => uniqid()]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
    }

    private function getSut(
        ?FieldMigrationServiceInterface $fieldMigrationService = null,
        ?MigrationReporterFactoryInterface $reporterFactory = null,
        ?MediaMigrationResultFilterInterface $resultFilter = null,
    ): MigrateMediaUrlsToIdsCommand {
        $fieldMigrationService ??= $this->createStub(FieldMigrationServiceInterface::class);
        $reporterFactory ??= $this->createStub(MigrationReporterFactoryInterface::class);
        $resultFilter ??= $this->createStub(MediaMigrationResultFilterInterface::class);

        return new MigrateMediaUrlsToIdsCommand(
            fieldMigrationService: $fieldMigrationService,
            reporterFactory: $reporterFactory,
            resultFilter: $resultFilter,
        );
    }
}
