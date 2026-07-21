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
        private readonly string $table,
        private readonly string $field,
        private readonly string $oxid,
        private readonly string $attribute,
        private readonly string $reference,
        private readonly MigrationOutcome $outcome,
        private readonly string $mediaId = '',
        private readonly string $detail = '',
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

    public function getOxid(): string
    {
        return $this->oxid;
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getOutcome(): MigrationOutcome
    {
        return $this->outcome;
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function getDetail(): string
    {
        return $this->detail;
    }
}
