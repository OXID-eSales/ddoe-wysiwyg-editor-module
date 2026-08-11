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

class MigrationReporterFactory implements MigrationReporterFactoryInterface
{
    public function __construct(
        private readonly ContextInterface $context,
    ) {
    }

    public function create(?string $reportFilePath): MigrationReporterInterface
    {
        if ($reportFilePath === null || $reportFilePath === '') {
            return new ScreenMigrationReporter();
        }

        return new CsvFileMigrationReporter($this->resolveReportFilePath($reportFilePath));
    }

    /**
     * Absolute paths are used as given, everything else lands in the shop log directory
     */
    private function resolveReportFilePath(string $reportFilePath): string
    {
        if (str_starts_with($reportFilePath, '/')) {
            return $reportFilePath;
        }

        return dirname($this->context->getLogFilePath()) . '/' . $reportFilePath;
    }
}
