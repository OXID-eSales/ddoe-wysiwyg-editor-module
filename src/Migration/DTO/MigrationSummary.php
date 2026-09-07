<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

class MigrationSummary implements MigrationSummaryInterface
{
    /**
     * @param string[] $lines
     */
    public function __construct(
        private readonly array $lines,
    ) {
    }

    public function getLines(): array
    {
        return $this->lines;
    }
}
