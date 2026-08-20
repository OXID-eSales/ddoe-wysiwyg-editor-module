<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Reporter;

use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScreenMigrationReporter implements MigrationReporterInterface
{
    public function report(MigrationReportInterface $report, OutputInterface $output): void
    {
        $failures = $report->getEntries(MigrationOutcome::Failed);

        $output->writeln(sprintf(
            '<info>%s::%s (key %s)</info>',
            $report->getTable(),
            $report->getField(),
            $report->getTableKey()
        ));
        $output->writeln(sprintf('Media references found: %d', count($report->getEntries())));
        $output->writeln(
            sprintf('Converted:              %d', count($report->getEntries(MigrationOutcome::Converted)))
        );
        $output->writeln(sprintf('Failed:                 %d', count($failures)));

        if (!$failures) {
            return;
        }

        $output->writeln('');

        foreach ($failures as $failure) {
            $output->writeln(sprintf(
                '  [%s=%s] %s="%s": %s',
                $report->getTableKey(),
                $failure->getKey(),
                $failure->getAttribute(),
                $failure->getPath(),
                $failure->getDetail()
            ));
        }

        $output->writeln('');
        $output->writeln(
            '<comment>These references were left unchanged. Either the media has to be added to the media'
            . ' library, or the path in the content is wrong - check whether the file exists under'
            . ' out/pictures/ddmedia.</comment>'
        );
    }
}
