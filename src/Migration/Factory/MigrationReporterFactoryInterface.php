<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\Factory;

use OxidEsales\WysiwygModule\Migration\Reporter\MigrationReporterInterface;

interface MigrationReporterFactoryInterface
{
    public function create(?string $reportFilePath): MigrationReporterInterface;
}
