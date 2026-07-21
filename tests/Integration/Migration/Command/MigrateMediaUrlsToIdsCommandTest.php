<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Integration\Migration\Command;

use Composer\Console\Application;
use OxidEsales\WysiwygModule\Migration\Command\MigrateMediaUrlsToIdsCommand;
use OxidEsales\WysiwygModule\Migration\Service\MigrationReport;
use OxidEsales\WysiwygModule\Migration\Service\MigrationReportCsvWriterInterface;
use OxidEsales\WysiwygModule\Migration\Repository\FieldMigrationRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class MigrateMediaUrlsToIdsCommandTest extends TestCase
{
    #[Test]
    public function migrationCallsRepositoryWithCorrectParams(): void
    {
        $table = uniqid();
        $field = uniqid();
        $tableKey = uniqid();

        $repositorySpy = $this->createMock(FieldMigrationRepositoryInterface::class);
        $repositorySpy->expects($this->once())
            ->method('migrateTableField')
            ->with($table, $field, $tableKey);

        $sut = $this->getSut($repositorySpy);

        $commandTester = $this->runCommand($sut, [
            'table' => $table,
            'field' => $field,
            'tableKey' => $tableKey,
        ]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString("$table::$field (key $tableKey)", $commandTester->getDisplay());
    }

    #[Test]
    public function migrationCallsRepositoryWithDefaultTableKey(): void
    {
        $table = uniqid();
        $field = uniqid();
        $tableKey = 'OXID';

        $repositorySpy = $this->createMock(FieldMigrationRepositoryInterface::class);
        $repositorySpy->expects($this->once())
            ->method('migrateTableField')
            ->with($table, $field, $tableKey);

        $sut = $this->getSut($repositorySpy);

        $commandTester = $this->runCommand($sut, [
            'table' => $table,
            'field' => $field,
        ]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString("$table::$field (key $tableKey)", $commandTester->getDisplay());
    }

    private function getSut(FieldMigrationRepositoryInterface $repository): MigrateMediaUrlsToIdsCommand
    {
        return new MigrateMediaUrlsToIdsCommand(
            fieldMigrationRepository: $repository,
            report: new MigrationReport(),
            reportCsvWriter: $this->createStub(MigrationReportCsvWriterInterface::class),
        );
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
