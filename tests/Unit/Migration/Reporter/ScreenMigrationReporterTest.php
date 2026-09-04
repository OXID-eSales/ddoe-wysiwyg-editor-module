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
        $converted = [
            $this->createStub(MediaMigrationResultInterface::class),
            $this->createStub(MediaMigrationResultInterface::class),
        ];

        $reportStub = $this->createConfiguredStub(MigrationReportInterface::class, [
            'getTable' => self::TABLE,
            'getField' => self::FIELD,
            'getTableKey' => self::TABLE_KEY,
            'getEntries' => $converted,
        ]);

        $filterStub = $this->createStub(MediaMigrationResultFilterInterface::class);
        $filterStub->method('filterByOutcome')->willReturnMap([
            [$converted, MigrationOutcome::Converted, $converted],
            [$converted, MigrationOutcome::Failed, []],
        ]);

        $outputSpy = $this->createMock(OutputInterface::class);
        $outputSpy->expects($this->once())
            ->method('writeln')
            ->with([
                '<info>oxcontents::OXCONTENT (key OXID)</info>',
                'Media references found: 2',
                'Converted:              2',
                'Failed:                 0',
            ]);

        $sut = $this->getSut($filterStub);

        $sut->report($reportStub, $outputSpy);
    }

    #[Test]
    public function reportListsEveryFailureWithItsRowAndReason(): void
    {
        $converted = [$this->createStub(MediaMigrationResultInterface::class)];
        $failures = [
            $this->createConfiguredStub(MediaMigrationResultInterface::class, [
                'getKey' => 'oxstartslot1',
                'getAttribute' => 'src',
                'getPath' => '/out/pictures/ddmedia/missing.jpg',
                'getDetail' => 'no matching entry in the media library',
            ]),
            $this->createConfiguredStub(MediaMigrationResultInterface::class, [
                'getKey' => 'oxstartslot2',
                'getAttribute' => 'href',
                'getPath' => 'https://shop.example/pic.jpg',
                'getDetail' => 'not recognized as a media library path',
            ]),
        ];
        $entries = [...$converted, ...$failures];

        $reportStub = $this->createConfiguredStub(MigrationReportInterface::class, [
            'getTable' => self::TABLE,
            'getField' => self::FIELD,
            'getTableKey' => self::TABLE_KEY,
            'getEntries' => $entries,
        ]);

        $filterStub = $this->createStub(MediaMigrationResultFilterInterface::class);
        $filterStub->method('filterByOutcome')->willReturnMap([
            [$entries, MigrationOutcome::Converted, $converted],
            [$entries, MigrationOutcome::Failed, $failures],
        ]);

        $outputSpy = $this->createMock(OutputInterface::class);
        $outputSpy->expects($this->once())
            ->method('writeln')
            ->with([
                '<info>oxcontents::OXCONTENT (key OXID)</info>',
                'Media references found: 3',
                'Converted:              1',
                'Failed:                 2',
                '',
                '  [OXID=oxstartslot1] src="/out/pictures/ddmedia/missing.jpg": no matching entry in the media library',
                '  [OXID=oxstartslot2] href="https://shop.example/pic.jpg": not recognized as a media library path',
                '',
                '<comment>These references were left unchanged. Either the media has to be added'
                    . ' to the media library, or the path in the content is wrong - check whether the file exists under'
                    . ' out/pictures/ddmedia.</comment>',
            ]);

        $sut = $this->getSut($filterStub);

        $sut->report($reportStub, $outputSpy);
    }

    private function getSut(
        ?MediaMigrationResultFilterInterface $resultFilter = null
    ): MigrationReporterInterface {
        $resultFilter ??= $this->createStub(MediaMigrationResultFilterInterface::class);

        return new ScreenMigrationReporter(
            resultFilter: $resultFilter,
        );
    }
}
