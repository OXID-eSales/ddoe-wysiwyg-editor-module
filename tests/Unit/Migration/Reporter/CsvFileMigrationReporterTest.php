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
        $convertedEntry = $this->makeEntryStub(
            key: 'oxstartslot1',
            attribute: 'src',
            path: '/out/pictures/ddmedia/a.jpg',
            outcome: MigrationOutcome::Converted,
            mediaId: 'id-a',
        );
        $failedEntry = $this->makeEntryStub(
            key: 'oxstartslot2',
            attribute: 'href',
            path: '/out/pictures/ddmedia/missing.jpg',
            outcome: MigrationOutcome::Failed,
            detail: 'media not registered',
        );

        $sut = $this->getSut();
        $sut->report(
            $this->makeReportStub([$convertedEntry, $failedEntry], [$failedEntry]),
            $this->createStub(OutputInterface::class)
        );

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
        $failedEntry = $this->makeEntryStub(outcome: MigrationOutcome::Failed);

        $outputSpy = $this->createMock(OutputInterface::class);
        $outputSpy->expects($this->once())
            ->method('writeln')
            ->with('Report of 2 media references (1 failed) written to ' . self::PATH);

        $sut = $this->getSut();
        $sut->report($this->makeReportStub([$this->makeEntryStub(), $failedEntry], [$failedEntry]), $outputSpy);
    }

    #[Test]
    public function reportThrowsWhenTheFileCannotBeWritten(): void
    {
        $sut = $this->getSut(self::PATH_IN_MISSING_DIRECTORY);

        $this->expectException(RuntimeException::class);
        $sut->report($this->createStub(MigrationReportInterface::class), $this->createStub(OutputInterface::class));
    }

    /**
     * @param MediaMigrationResultInterface[] $entries
     * @param MediaMigrationResultInterface[] $failed
     */
    private function makeReportStub(array $entries, array $failed = []): MigrationReportInterface
    {
        $reportStub = $this->createStub(MigrationReportInterface::class);
        $reportStub->method('getTable')->willReturn(self::TABLE);
        $reportStub->method('getField')->willReturn(self::FIELD);
        $reportStub->method('getEntries')->willReturnCallback(
            static fn(?MigrationOutcome $outcome = null): array => match ($outcome) {
                MigrationOutcome::Failed => $failed,
                default => $entries,
            }
        );

        return $reportStub;
    }

    private function makeEntryStub(
        string $key = 'oxstartslot1',
        string $attribute = 'src',
        string $path = '/out/pictures/ddmedia/1.jpg',
        MigrationOutcome $outcome = MigrationOutcome::Converted,
        string $mediaId = '',
        string $detail = '',
    ): MediaMigrationResultInterface {
        $entryStub = $this->createStub(MediaMigrationResultInterface::class);
        $entryStub->method('getKey')->willReturn($key);
        $entryStub->method('getAttribute')->willReturn($attribute);
        $entryStub->method('getPath')->willReturn($path);
        $entryStub->method('getOutcome')->willReturn($outcome);
        $entryStub->method('getMediaId')->willReturn($mediaId);
        $entryStub->method('getDetail')->willReturn($detail);

        return $entryStub;
    }

    private function getSut(string $path = self::PATH): MigrationReporterInterface
    {
        return new CsvFileMigrationReporter($path);
    }
}
