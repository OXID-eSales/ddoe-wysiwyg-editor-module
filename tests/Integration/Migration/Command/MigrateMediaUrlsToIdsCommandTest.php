<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Integration\Migration\Command;

use Composer\Console\Application;
use OxidEsales\WysiwygModule\Migration\Command\MigrateMediaUrlsToIdsCommand;
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

        $sut = new MigrateMediaUrlsToIdsCommand(
            fieldMigrationRepository: $repositorySpy,
        );

        $application = new Application();
        $application->add($sut);

        $commandTester = new CommandTester($sut);
        $result = $commandTester->execute([
            'table' => $table,
            'field' => $field,
            'tableKey' => $tableKey,
        ]);

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertStringContainsString("Done for $table::$field using key $tableKey", $commandTester->getDisplay());
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

        $sut = new MigrateMediaUrlsToIdsCommand(
            fieldMigrationRepository: $repositorySpy,
        );

        $application = new Application();
        $application->add($sut);

        $commandTester = new CommandTester($sut);
        $result = $commandTester->execute([
            'table' => $table,
            'field' => $field,
        ]);

        $this->assertSame(Command::SUCCESS, $result);
        $this->assertStringContainsString("Done for $table::$field using key $tableKey", $commandTester->getDisplay());
    }
}
