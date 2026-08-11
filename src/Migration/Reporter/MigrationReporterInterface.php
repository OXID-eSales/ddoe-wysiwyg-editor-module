<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Reporter;

use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface MigrationReporterInterface
{
    public function report(MigrationReportInterface $report, OutputInterface $output): void;
}
