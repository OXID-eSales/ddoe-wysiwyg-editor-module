<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Reporter;

use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use OxidEsales\WysiwygModule\Migration\Reporter\MigrationReporterInterface;
use OxidEsales\WysiwygModule\Migration\Reporter\ScreenMigrationReporter;
use OxidEsales\WysiwygModule\Migration\Service\MediaMigrationResultFilterInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class ScreenMigrationReporterTest extends TestCase
{
    private const TABLE = 'oxcontents';
    private const FIELD = 'OXCONTENT';
    private const TABLE_KEY = 'OXID';

    #[Test]
    public function reportPrintsTheSummaryOfTheRun(): void
    {
        $lines = [];
        $converted = [$this->makeEntryStub(), $this->makeEntryStub()];
        $failures = [$this->makeEntryStub()];
        $entries = [...$converted, ...$failures];

        $sut = $this->getSut($entries, $converted, $failures);
        $sut->report($this->makeReportStub($entries), $this->makeOutputStub($lines));

        $display = implode(PHP_EOL, $lines);

        $this->assertStringContainsString(
            self::TABLE . '::' . self::FIELD . ' (key ' . self::TABLE_KEY . ')',
            $display
        );
        $this->assertMatchesRegularExpression('/^Media references found:\s+3$/m', $display);
        $this->assertMatchesRegularExpression('/^Converted:\s+2$/m', $display);
        $this->assertMatchesRegularExpression('/^Failed:\s+1$/m', $display);
    }

    #[Test]
    public function reportListsEveryFailureWithItsRowAndReason(): void
    {
        $lines = [];
        $failures = [
            $this->makeEntryStub(
                key: 'oxstartslot1',
                attribute: 'src',
                path: '/out/pictures/ddmedia/missing.jpg',
                detail: 'no matching entry in the media library',
            ),
            $this->makeEntryStub(
                key: 'oxstartslot2',
                attribute: 'href',
                path: 'https://shop.example/pic.jpg',
                detail: 'not recognized as a media library path',
            ),
        ];

        $sut = $this->getSut($failures, [], $failures);
        $sut->report($this->makeReportStub($failures), $this->makeOutputStub($lines));

        $display = implode(PHP_EOL, $lines);

        $this->assertMatchesRegularExpression('/^Failed:\s+2$/m', $display);
        $this->assertStringContainsString(
            '[OXID=oxstartslot1] src="/out/pictures/ddmedia/missing.jpg": no matching entry in the media library',
            $display
        );
        $this->assertStringContainsString(
            '[OXID=oxstartslot2] href="https://shop.example/pic.jpg": not recognized as a media library path',
            $display
        );
        $this->assertStringContainsString('check whether the file exists under out/pictures/ddmedia', $display);
    }

    #[Test]
    public function reportKeepsQuietAboutFailuresWhenEverythingWasConverted(): void
    {
        $lines = [];
        $converted = [$this->makeEntryStub()];

        $sut = $this->getSut($converted, $converted, []);
        $sut->report($this->makeReportStub($converted), $this->makeOutputStub($lines));

        $display = implode(PHP_EOL, $lines);

        $this->assertMatchesRegularExpression('/^Failed:\s+0$/m', $display);
        $this->assertStringNotContainsString('These references were left unchanged', $display);
    }

    /**
     * @param MediaMigrationResultInterface[] $entries
     */
    private function makeReportStub(array $entries): MigrationReportInterface
    {
        $reportStub = $this->createStub(MigrationReportInterface::class);
        $reportStub->method('getTable')->willReturn(self::TABLE);
        $reportStub->method('getField')->willReturn(self::FIELD);
        $reportStub->method('getTableKey')->willReturn(self::TABLE_KEY);
        $reportStub->method('getEntries')->willReturn($entries);

        return $reportStub;
    }

    private function makeEntryStub(
        string $key = 'oxstartslot1',
        string $attribute = 'src',
        string $path = '/out/pictures/ddmedia/1.jpg',
        string $detail = '',
    ): MediaMigrationResultInterface {
        $entryStub = $this->createStub(MediaMigrationResultInterface::class);
        $entryStub->method('getKey')->willReturn($key);
        $entryStub->method('getAttribute')->willReturn($attribute);
        $entryStub->method('getPath')->willReturn($path);
        $entryStub->method('getDetail')->willReturn($detail);

        return $entryStub;
    }

    /**
     * Collects everything the reporter writes, so the printed report can be asserted as a whole.
     *
     * @param string[] $lines
     */
    private function makeOutputStub(array &$lines): OutputInterface
    {
        $outputStub = $this->createStub(OutputInterface::class);
        $outputStub->method('writeln')->willReturnCallback(
            static function (string $message) use (&$lines): void {
                $lines[] = $message;
            }
        );

        return $outputStub;
    }

    /**
     * @param MediaMigrationResultInterface[] $entries
     * @param MediaMigrationResultInterface[] $converted
     * @param MediaMigrationResultInterface[] $failed
     */
    private function getSut(array $entries = [], array $converted = [], array $failed = []): MigrationReporterInterface
    {
        $filterStub = $this->createStub(MediaMigrationResultFilterInterface::class);
        $filterStub->method('filterByOutcome')->willReturnMap([
            [$entries, MigrationOutcome::Converted, $converted],
            [$entries, MigrationOutcome::Failed, $failed],
        ]);

        return new ScreenMigrationReporter($filterStub);
    }
}
