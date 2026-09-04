<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Reporter;

use org\bovigo\vfs\vfsStream;
use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use OxidEsales\WysiwygModule\Migration\Reporter\CsvFileMigrationReporter;
use OxidEsales\WysiwygModule\Migration\Reporter\MigrationReporterInterface;
use OxidEsales\WysiwygModule\Migration\Service\MediaMigrationResultFilterInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

class CsvFileMigrationReporterTest extends TestCase
{
    private const TABLE = 'oxcontents';
    private const FIELD = 'OXCONTENT';
    private const PATH = 'vfs://root/media-migration.csv';
    private const PATH_IN_MISSING_DIRECTORY = 'vfs://root/does/not/exist.csv';

    protected function setUp(): void
    {
        parent::setUp();

        vfsStream::setup('root');
    }

    #[Test]
    public function reportWritesHeaderAndOneRowPerEntry(): void
    {
        $convertedEntry = $this->createConfiguredStub(MediaMigrationResultInterface::class, [
            'getKey' => 'oxstartslot1',
            'getAttribute' => 'src',
            'getPath' => '/out/pictures/ddmedia/a.jpg',
            'getOutcome' => MigrationOutcome::Converted,
            'getMediaId' => 'id-a',
            'getDetail' => '',
        ]);
        $failedEntry = $this->createConfiguredStub(MediaMigrationResultInterface::class, [
            'getKey' => 'oxstartslot2',
            'getAttribute' => 'href',
            'getPath' => '/out/pictures/ddmedia/missing.jpg',
            'getOutcome' => MigrationOutcome::Failed,
            'getMediaId' => '',
            'getDetail' => 'media not registered',
        ]);

        $reportStub = $this->createConfiguredStub(MigrationReportInterface::class, [
            'getTable' => self::TABLE,
            'getField' => self::FIELD,
            'getEntries' => [$convertedEntry, $failedEntry],
        ]);

        $filterStub = $this->createConfiguredStub(
            MediaMigrationResultFilterInterface::class,
            ['filterByOutcome' => [$failedEntry]]
        );

        $sut = $this->getSut(
            resultFilter: $filterStub
        );
        $sut->report($reportStub, $this->createStub(OutputInterface::class));

        $rows = array_map('str_getcsv', file(self::PATH, FILE_IGNORE_NEW_LINES));

        $this->assertCount(3, $rows, 'header + two entries');
        $this->assertSame(
            ['table', 'field', 'key', 'attribute', 'path', 'outcome', 'media_id', 'detail'],
            $rows[0]
        );
        $this->assertSame(
            [
                self::TABLE,
                self::FIELD,
                'oxstartslot1',
                'src',
                '/out/pictures/ddmedia/a.jpg',
                'converted',
                'id-a',
                '',
            ],
            $rows[1]
        );
        $this->assertSame(
            [
                self::TABLE,
                self::FIELD,
                'oxstartslot2',
                'href',
                '/out/pictures/ddmedia/missing.jpg',
                'failed',
                '',
                'media not registered',
            ],
            $rows[2]
        );
    }

    #[Test]
    public function reportWritesTheHeaderOnlyWhenThereIsNothingToReport(): void
    {
        $sut = $this->getSut();
        $sut->report($this->createStub(MigrationReportInterface::class), $this->createStub(OutputInterface::class));

        $this->assertCount(1, file(self::PATH, FILE_IGNORE_NEW_LINES));
    }

    #[Test]
    public function reportTellsWhereTheReportWasWritten(): void
    {
        $convertedEntry = $this->createConfiguredStub(
            MediaMigrationResultInterface::class,
            ['getOutcome' => MigrationOutcome::Converted]
        );
        $failedEntry = $this->createConfiguredStub(
            MediaMigrationResultInterface::class,
            ['getOutcome' => MigrationOutcome::Failed]
        );
        $reportStub = $this->createConfiguredStub(
            MigrationReportInterface::class,
            ['getEntries' => [$convertedEntry, $failedEntry]]
        );

        $outputSpy = $this->createMock(OutputInterface::class);
        $outputSpy->expects($this->once())
            ->method('writeln')
            ->with('Report of 2 media references (1 failed) written to ' . self::PATH);

        $filterStub = $this->createConfiguredStub(
            MediaMigrationResultFilterInterface::class,
            ['filterByOutcome' => [$failedEntry]]
        );

        $sut = $this->getSut(
            resultFilter: $filterStub
        );

        $sut->report($reportStub, $outputSpy);
    }

    #[Test]
    public function reportThrowsWhenTheFileCannotBeWritten(): void
    {
        $sut = $this->getSut(
            path: self::PATH_IN_MISSING_DIRECTORY
        );

        $this->expectException(RuntimeException::class);
        $sut->report($this->createStub(MigrationReportInterface::class), $this->createStub(OutputInterface::class));
    }

    private function getSut(
        ?MediaMigrationResultFilterInterface $resultFilter = null,
        string $path = self::PATH
    ): MigrationReporterInterface {
        $resultFilter ??= $this->createStub(MediaMigrationResultFilterInterface::class);

        return new CsvFileMigrationReporter(
            resultFilter: $resultFilter,
            path: $path,
        );
    }
}
