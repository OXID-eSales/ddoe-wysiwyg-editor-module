<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

final class TableField implements TableFieldInterface
{
    public function __construct(
        private readonly string $table,
        private readonly string $field,
        private readonly string $keyField,
    ) {
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getKeyField(): string
    {
        return $this->keyField;
    }
}
