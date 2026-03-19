<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Integration\Migration\Command;

use Composer\Console\Application;
use OxidEsales\WysiwygModule\Migration\Command\MigrateMediaAltTextsCommand;
use OxidEsales\WysiwygModule\Migration\Repository\AltTextMigrationRepositoryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class MigrateMediaAltTextsCommandTest extends TestCase
{
    #[Test]
    public function migrationCallsRepositoryWithCorrectParams(): void
    {
        $table = uniqid();
        $field = uniqid();
        $tableKey = uniqid();

        $repositorySpy = $this->createMock(AltTextMigrationRepositoryInterface::class);
        $repositorySpy->expects($this->once())
            ->method('migrateTableField')
            ->with($table, $field, $tableKey)
            ->willReturn([]);

        $sut = new MigrateMediaAltTextsCommand(
            altTextMigrationRepository: $repositorySpy,
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

        $repositorySpy = $this->createMock(AltTextMigrationRepositoryInterface::class);
        $repositorySpy->expects($this->once())
            ->method('migrateTableField')
            ->with($table, $field, $tableKey)
            ->willReturn([]);

        $sut = new MigrateMediaAltTextsCommand(
            altTextMigrationRepository: $repositorySpy,
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

    #[Test]
    public function customAltTextWarningsAreDisplayed(): void
    {
        $table = uniqid();
        $field = uniqid();

        $repositoryStub = $this->createMock(AltTextMigrationRepositoryInterface::class);
        $repositoryStub->method('migrateTableField')
            ->willReturn([
                [
                    'key' => 'row123',
                    'tag' => '<img>',
                    'mediaId' => 'media456',
                    'altText' => 'My custom alt',
                ],
            ]);

        $sut = new MigrateMediaAltTextsCommand(
            altTextMigrationRepository: $repositoryStub,
        );

        $application = new Application();
        $application->add($sut);

        $commandTester = new CommandTester($sut);
        $commandTester->execute([
            'table' => $table,
            'field' => $field,
        ]);

        $display = $commandTester->getDisplay();
        $this->assertStringContainsString('custom alt text that was NOT modified', $display);
        $this->assertStringContainsString('[OXID=row123]', $display);
        $this->assertStringContainsString('media-id="media456"', $display);
        $this->assertStringContainsString('alt="My custom alt"', $display);
    }
}
