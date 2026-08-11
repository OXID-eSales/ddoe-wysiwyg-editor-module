<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Integration\Migration\Command;

use Composer\Console\Application;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\WysiwygModule\Migration\Command\MigrateMediaUrlsToIdsCommand;
use OxidEsales\WysiwygModule\Migration\DTO\MediaReferenceResult;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReport;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportEntry;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use OxidEsales\WysiwygModule\Migration\Factory\MigrationReporterFactory;
use OxidEsales\WysiwygModule\Migration\Service\FieldMigrationServiceInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
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
            ->willReturn($this->makeReport($table, $field, $tableKey));

        $commandTester = $this->runCommand($this->getSut($serviceSpy), [
            'table' => $table,
            'field' => $field,
            'tableKey' => $tableKey,
        ]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString("$table::$field (key $tableKey)", $commandTester->getDisplay());
    }

    #[Test]
    public function migrationCallsServiceWithDefaultTableKey(): void
    {
        $table = uniqid();
        $field = uniqid();
        $tableKey = 'OXID';

        $serviceSpy = $this->createMock(FieldMigrationServiceInterface::class);
        $serviceSpy->expects($this->once())
            ->method('migrate')
            ->with($table, $field, $tableKey)
            ->willReturn($this->makeReport($table, $field, $tableKey));

        $commandTester = $this->runCommand($this->getSut($serviceSpy), [
            'table' => $table,
            'field' => $field,
        ]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString("$table::$field (key $tableKey)", $commandTester->getDisplay());
    }

    #[Test]
    public function migrationWritesTheReportToTheRequestedFile(): void
    {
        $table = uniqid();
        $field = uniqid();
        $path = sys_get_temp_dir() . '/wysiwyg-media-migration-' . uniqid() . '.csv';

        $serviceStub = $this->createStub(FieldMigrationServiceInterface::class);
        $serviceStub->method('migrate')->willReturn($this->makeReport($table, $field, 'OXID'));

        try {
            $commandTester = $this->runCommand($this->getSut($serviceStub), [
                'table' => $table,
                'field' => $field,
                '--report-file' => $path,
            ]);

            $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
            $this->assertStringContainsString($path, $commandTester->getDisplay());
            $this->assertFileExists($path);
        } finally {
            @unlink($path);
        }
    }

    #[Test]
    public function migrationFailsWhenReferencesCouldNotBeConverted(): void
    {
        $table = uniqid();
        $field = uniqid();

        $failedEntry = new MigrationReportEntry(
            key: uniqid(),
            reference: new MediaReferenceResult(
                attribute: 'src',
                path: '/out/pictures/ddmedia/missing.jpg',
                outcome: MigrationOutcome::Failed,
                detail: 'media not registered',
            ),
        );

        $serviceStub = $this->createStub(FieldMigrationServiceInterface::class);
        $serviceStub->method('migrate')->willReturn($this->makeReport($table, $field, 'OXID', [$failedEntry]));

        $commandTester = $this->runCommand($this->getSut($serviceStub), [
            'table' => $table,
            'field' => $field,
        ]);

        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('media not registered', $commandTester->getDisplay());
    }

    /**
     * @param MigrationReportEntry[] $entries
     */
    private function makeReport(
        string $table,
        string $field,
        string $tableKey,
        array $entries = []
    ): MigrationReportInterface {
        return new MigrationReport(
            table: $table,
            field: $field,
            tableKey: $tableKey,
            entries: $entries,
        );
    }

    private function getSut(FieldMigrationServiceInterface $fieldMigrationService): MigrateMediaUrlsToIdsCommand
    {
        return new MigrateMediaUrlsToIdsCommand(
            fieldMigrationService: $fieldMigrationService,
            reporterFactory: new MigrationReporterFactory($this->makeContextStub()),
        );
    }

    private function makeContextStub(): ContextInterface
    {
        $contextStub = $this->createStub(ContextInterface::class);
        $contextStub->method('getLogFilePath')->willReturn(sys_get_temp_dir() . '/oxideshop.log');

        return $contextStub;
    }

    private function runCommand(MigrateMediaUrlsToIdsCommand $sut, array $arguments): CommandTester
    {
        $application = new Application();
        $application->add($sut);

        $commandTester = new CommandTester($sut);
        $commandTester->execute($arguments);

        return $commandTester;
    }
}
