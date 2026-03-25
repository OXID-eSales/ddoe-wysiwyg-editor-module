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

        $repositoryMock = $this->createMock(AltTextMigrationRepositoryInterface::class);
        $repositoryMock->expects($this->once())
            ->method('migrateTableField')
            ->with($table, $field, $tableKey)
            ->willReturn([]);

        $commandTester = new CommandTester($this->getSut($repositoryMock));
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

        $repositoryMock = $this->createMock(AltTextMigrationRepositoryInterface::class);
        $repositoryMock->expects($this->once())
            ->method('migrateTableField')
            ->with($table, $field, $tableKey)
            ->willReturn([]);

        $commandTester = new CommandTester($this->getSut($repositoryMock));
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
        $key = uniqid();
        $mediaId = uniqid();
        $altText = uniqid();

        $repositoryStub = $this->createMock(AltTextMigrationRepositoryInterface::class);
        $repositoryStub->method('migrateTableField')
            ->willReturn([
                [
                    'key' => $key,
                    'tag' => '<img>',
                    'mediaId' => $mediaId,
                    'altText' => $altText,
                ],
            ]);

        $commandTester = new CommandTester($this->getSut($repositoryStub));
        $commandTester->execute([
            'table' => $table,
            'field' => $field,
        ]);

        $display = $commandTester->getDisplay();
        $this->assertStringContainsString('custom alt text that was NOT modified', $display);
        $this->assertStringContainsString("[OXID=$key]", $display);
        $this->assertStringContainsString("media-id=\"$mediaId\"", $display);
        $this->assertStringContainsString("alt=\"$altText\"", $display);
    }

    private function getSut(AltTextMigrationRepositoryInterface $repository): MigrateMediaAltTextsCommand
    {
        $sut = new MigrateMediaAltTextsCommand(
            altTextMigrationRepository: $repository,
        );

        $application = new Application();
        $application->add($sut);

        return $sut;
    }
}
