<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

class MigrationReportEntry
{
    public function __construct(
        private readonly string $key,
        private readonly MediaReferenceResultInterface $reference,
    ) {
    }

    /**
     * The value of the table key identifying the migrated row.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    public function getReference(): MediaReferenceResultInterface
    {
        return $this->reference;
    }
}
