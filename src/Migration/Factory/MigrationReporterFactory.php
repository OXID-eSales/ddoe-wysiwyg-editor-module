<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Factory;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\WysiwygModule\Migration\Reporter\CsvFileMigrationReporter;
use OxidEsales\WysiwygModule\Migration\Reporter\MigrationReporterInterface;
use OxidEsales\WysiwygModule\Migration\Reporter\ScreenMigrationReporter;
use OxidEsales\WysiwygModule\Migration\Service\MediaMigrationResultFilterInterface;

class MigrationReporterFactory implements MigrationReporterFactoryInterface
{
    public function __construct(
        private readonly ContextInterface $context,
        private readonly MediaMigrationResultFilterInterface $resultFilter,
    ) {
    }

    /**
     * @todo-high: not all reporters need the path
     * @todo-high: the filter is injected here, but its not used, just transfered.
     * @todo-high: as well as the context, used for path calculation in csv reporter case.
     */
    public function create(?string $reportFilePath): MigrationReporterInterface
    {
        if ($reportFilePath === null || $reportFilePath === '') {
            return new ScreenMigrationReporter($this->resultFilter);
        }

        return new CsvFileMigrationReporter($this->resultFilter, $this->resolveReportFilePath($reportFilePath));
    }

    /**
     * Absolute paths are used as given, everything else lands in the shop log directory
     *
     * @todo-high: this calculation should not be here at all.
     */
    private function resolveReportFilePath(string $reportFilePath): string
    {
        if (str_starts_with($reportFilePath, '/')) {
            return $reportFilePath;
        }

        return dirname($this->context->getLogFilePath()) . '/' . $reportFilePath;
    }
}
