<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

class MigrationReport implements MigrationReportInterface
{
    /**
     * @param MediaMigrationResultInterface[] $entries
     */
    public function __construct(
        private readonly TableFieldInterface $tableField,
        private readonly array $entries,
    ) {
    }

    public function getTableField(): TableFieldInterface
    {
        return $this->tableField;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }
}
