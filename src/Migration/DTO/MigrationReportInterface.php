<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\DTO;

/**
 * The immutable result of one migration run, handed to a reporter for output.
 */
interface MigrationReportInterface
{
    public function getTableField(): TableFieldInterface;

    /**
     * Every media reference the migration came across, converted or not.
     *
     * @return MediaMigrationResultInterface[]
     */
    public function getEntries(): array;
}
