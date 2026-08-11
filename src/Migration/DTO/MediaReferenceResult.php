<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

/**
 * The outcome of one media reference found in migrated content.
 */
class MediaReferenceResult implements MediaReferenceResultInterface
{
    public function __construct(
        private readonly string $attribute,
        private readonly string $path,
        private readonly MigrationOutcome $outcome,
        private readonly string $mediaId = '',
        private readonly string $detail = '',
    ) {
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getPath(): string
    {
        return $this->path;
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
