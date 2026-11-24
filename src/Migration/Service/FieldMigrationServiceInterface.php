<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use OxidEsales\WysiwygModule\Migration\DTO\TableFieldInterface;

interface FieldMigrationServiceInterface
{
    /**
     * Migrates the media references of one table field and reports what happened.
     */
    public function migrate(TableFieldInterface $tableField): MigrationReportInterface;
}
