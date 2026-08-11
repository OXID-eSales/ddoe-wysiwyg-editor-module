<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Factory;

use OxidEsales\WysiwygModule\Migration\Reporter\MigrationReporterInterface;

interface MigrationReporterFactoryInterface
{
    /**
     * Creates the reporter to hand the migration report to: a csv file writer when a report
     * file path is given, the screen reporter otherwise.
     */
    public function create(?string $reportFilePath): MigrationReporterInterface;
}
