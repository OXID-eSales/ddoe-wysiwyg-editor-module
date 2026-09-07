<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\DTO;

interface MigrationSummaryInterface
{
    /**
     * @return string[]
     */
    public function getLines(): array;
}
