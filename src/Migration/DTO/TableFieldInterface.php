<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\DTO;

/**
 * The table field a migration runs on, with the column identifying its rows.
 */
interface TableFieldInterface
{
    public function getTable(): string;

    public function getField(): string;

    public function getKeyField(): string;
}
