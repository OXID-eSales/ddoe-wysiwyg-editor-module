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
    public function create(?string $reportFilePath): MigrationReporterInterface;
}
