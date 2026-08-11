<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Reporter;

use OxidEsales\WysiwygModule\Migration\DTO\MediaReferenceResult;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReport;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportEntry;
use OxidEsales\WysiwygModule\Migration\Reporter\ScreenMigrationReporter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class ScreenMigrationReporterTest extends TestCase
{
    #[Test]
    public function reportPrintsTheSummaryOfTheRun(): void
    {
        $output = new BufferedOutput();

        $sut = new ScreenMigrationReporter();
        $sut->report(
            $this->makeReport([
                $this->makeEntry('row1', MigrationOutcome::Converted),
                $this->makeEntry('row2', MigrationOutcome::Converted),
            ]),
            $output
        );

        $display = $output->fetch();

        $this->assertStringContainsString('oxcontents::OXCONTENT (key OXID)', $display);
        $this->assertStringContainsString('Media references found: 2', $display);
        $this->assertStringContainsString('Converted:              2', $display);
        $this->assertStringContainsString('Failed:                 0', $display);
    }

    #[Test]
    public function reportListsEveryFailureWithItsRowAndReason(): void
    {
        $output = new BufferedOutput();

        $sut = new ScreenMigrationReporter();
        $sut->report(
            $this->makeReport([
                $this->makeEntry('oxstartslot1', MigrationOutcome::Failed, 'no matching entry in the media library'),
            ]),
            $output
        );

        $display = $output->fetch();

        $this->assertStringContainsString('Failed:                 1', $display);
        $this->assertStringContainsString(
            '[OXID=oxstartslot1] src="/out/pictures/ddmedia/missing.jpg": no matching entry in the media library',
            $display
        );
        $this->assertStringContainsString('check whether the file exists under out/pictures/ddmedia', $display);
    }

    /**
     * @param MigrationReportEntry[] $entries
     */
    private function makeReport(array $entries): MigrationReport
    {
        return new MigrationReport(
            table: 'oxcontents',
            field: 'OXCONTENT',
            tableKey: 'OXID',
            entries: $entries,
        );
    }

    private function makeEntry(string $key, MigrationOutcome $outcome, string $detail = ''): MigrationReportEntry
    {
        return new MigrationReportEntry(
            key: $key,
            reference: new MediaReferenceResult(
                attribute: 'src',
                path: '/out/pictures/ddmedia/missing.jpg',
                outcome: $outcome,
                detail: $detail,
            ),
        );
    }
}
