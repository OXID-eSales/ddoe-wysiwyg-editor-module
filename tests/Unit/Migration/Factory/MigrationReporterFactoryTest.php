<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Factory;

use org\bovigo\vfs\vfsStream;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use OxidEsales\WysiwygModule\Migration\Factory\MigrationReporterFactory;
use OxidEsales\WysiwygModule\Migration\Factory\MigrationReporterFactoryInterface;
use OxidEsales\WysiwygModule\Migration\Reporter\CsvFileMigrationReporter;
use OxidEsales\WysiwygModule\Migration\Reporter\ScreenMigrationReporter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationReporterFactoryTest extends TestCase
{
    private const REPORT_FILE_NAME = 'media-migration.csv';
    private const LOG_DIRECTORY = 'log';
    private const LOG_FILE_PATH = 'vfs://root/' . self::LOG_DIRECTORY . '/oxideshop.log';
    private const REPORT_IN_LOG_DIRECTORY = 'vfs://root/' . self::LOG_DIRECTORY . '/' . self::REPORT_FILE_NAME;

    protected function setUp(): void
    {
        parent::setUp();

        vfsStream::setup('root', null, [self::LOG_DIRECTORY => []]);
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
        $sut = $this->getSut();

        $reporter = $sut->create($reportFilePath);

        $this->assertInstanceOf(ScreenMigrationReporter::class, $reporter);
    }

    #[Test]
    public function createReturnsCsvFileReporterWhenAReportFileIsRequested(): void
    {
        $sut = $this->getSut();

        $reporter = $sut->create(self::REPORT_FILE_NAME);

        $this->assertInstanceOf(CsvFileMigrationReporter::class, $reporter);
    }

    #[Test]
    public function relativeReportFileIsWrittenToTheShopLogDirectory(): void
    {
        $sut = $this->getSut();

        $reporter = $sut->create(self::REPORT_FILE_NAME);
        $reporter->report(
            $this->createStub(MigrationReportInterface::class),
            $this->createStub(OutputInterface::class)
        );

        $this->assertFileExists(self::REPORT_IN_LOG_DIRECTORY);
    }

    #[Test]
    public function absoluteReportFileIsWrittenWhereItWasAskedFor(): void
    {
        // an absolute path cannot be expressed as a vfsstream url, so this one branch needs a real file
        $path = sys_get_temp_dir() . '/wysiwyg-migration-' . uniqid() . '.csv';
        $sut = $this->getSut();

        try {
            $reporter = $sut->create($path);
            $reporter->report(
                $this->createStub(MigrationReportInterface::class),
                $this->createStub(OutputInterface::class)
            );

            $this->assertFileExists($path);
            $this->assertFileDoesNotExist('vfs://root/' . self::LOG_DIRECTORY . '/' . basename($path));
        } finally {
            @unlink($path);
        }
    }

    private function getSut(): MigrationReporterFactoryInterface
    {
        $contextStub = $this->createStub(ContextInterface::class);
        $contextStub->method('getLogFilePath')->willReturn(self::LOG_FILE_PATH);

        return new MigrationReporterFactory($contextStub);
    }
}
