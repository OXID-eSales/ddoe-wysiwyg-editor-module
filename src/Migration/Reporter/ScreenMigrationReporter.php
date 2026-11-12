<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Reporter;

use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationSummary;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationSummaryInterface;
use OxidEsales\WysiwygModule\Migration\Service\MediaMigrationResultFilterInterface;

class ScreenMigrationReporter implements MigrationReporterInterface
{
    private const FAILURE_HINT = '<comment>These references were left unchanged. Either the media has to be added'
        . ' to the media library, or the path in the content is wrong - check whether the file exists under'
        . ' out/pictures/ddmedia.</comment>';

    public function __construct(
        private readonly MediaMigrationResultFilterInterface $resultFilter,
    ) {
    }

    public function report(MigrationReportInterface $report): MigrationSummaryInterface
    {
        $tableField = $report->getTableField();
        $entries = $report->getEntries();
        $converted = $this->resultFilter->filterByOutcome($entries, MigrationOutcome::Converted);
        $failures = $this->resultFilter->filterByOutcome($entries, MigrationOutcome::Failed);

        return new MigrationSummary(
            lines: [
                sprintf(
                    '<info>%s::%s (key %s)</info>',
                    $tableField->getTable(),
                    $tableField->getField(),
                    $tableField->getKeyField()
                ),
                sprintf('Media references found: %d', count($entries)),
                sprintf('Converted:              %d', count($converted)),
                sprintf('Failed:                 %d', count($failures)),
                ...$this->failureLines($tableField->getKeyField(), $failures),
            ],
        );
    }

    /**
     * @param MediaMigrationResultInterface[] $failures
     *
     * @return string[]
     */
    private function failureLines(string $keyField, array $failures): array
    {
        if (!$failures) {
            return [];
        }

        $lines = [''];

        foreach ($failures as $failure) {
            $lines[] = sprintf(
                '  [%s=%s] %s="%s": %s',
                $keyField,
                $failure->getKey(),
                $failure->getAttribute(),
                $failure->getPath(),
                $failure->getDetail()
            );
        }

        $lines[] = '';
        $lines[] = self::FAILURE_HINT;

        return $lines;
    }
}
