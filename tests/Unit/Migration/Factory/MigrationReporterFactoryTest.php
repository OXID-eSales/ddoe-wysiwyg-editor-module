<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Factory;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReport;
use OxidEsales\WysiwygModule\Migration\Factory\MigrationReporterFactory;
use OxidEsales\WysiwygModule\Migration\Reporter\CsvFileMigrationReporter;
use OxidEsales\WysiwygModule\Migration\Reporter\ScreenMigrationReporter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class MigrationReporterFactoryTest extends TestCase
{
    private string $logDirectory = '';

    protected function setUp(): void
    {
        parent::setUp();

        $this->logDirectory = sys_get_temp_dir() . '/wysiwyg-migration-log-' . uniqid();
        mkdir($this->logDirectory);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->logDirectory . '/*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->logDirectory);

        parent::tearDown();
    }

    public static function noReportFileDataProvider(): \Generator
    {
        yield 'no option given' => ['reportFilePath' => null];
        yield 'empty option given' => ['reportFilePath' => ''];
    }

    #[Test]
    #[DataProvider('noReportFileDataProvider')]
    public function createReturnsScreenReporterWhenNoReportFileIsRequested(?string $reportFilePath): void
    {
        $this->assertInstanceOf(ScreenMigrationReporter::class, $this->getSut()->create($reportFilePath));
    }

    #[Test]
    public function createReturnsCsvFileReporterWhenAReportFileIsRequested(): void
    {
        $this->assertInstanceOf(CsvFileMigrationReporter::class, $this->getSut()->create('media-migration.csv'));
    }

    #[Test]
    public function relativeReportFileIsWrittenToTheShopLogDirectory(): void
    {
        $reporter = $this->getSut()->create('media-migration.csv');

        $reporter->report($this->makeEmptyReport(), new BufferedOutput());

        $this->assertFileExists($this->logDirectory . '/media-migration.csv');
    }

    #[Test]
    public function absoluteReportFileIsWrittenWhereItWasAskedFor(): void
    {
        $path = sys_get_temp_dir() . '/wysiwyg-migration-' . uniqid() . '.csv';

        $reporter = $this->getSut()->create($path);

        try {
            $reporter->report($this->makeEmptyReport(), new BufferedOutput());

            $this->assertFileExists($path);
            $this->assertFileDoesNotExist($this->logDirectory . '/' . basename($path));
        } finally {
            @unlink($path);
        }
    }

    private function makeEmptyReport(): MigrationReport
    {
        return new MigrationReport(
            table: 'oxcontents',
            field: 'OXCONTENT',
            tableKey: 'OXID',
            entries: [],
        );
    }

    private function getSut(): MigrationReporterFactory
    {
        $contextStub = $this->createStub(ContextInterface::class);
        $contextStub->method('getLogFilePath')->willReturn($this->logDirectory . '/oxideshop.log');

        return new MigrationReporterFactory($contextStub);
    }
}
