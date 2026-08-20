<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

class MediaMigrationResult implements MediaMigrationResultInterface
{
    public function __construct(
        private readonly string $attribute,
        private readonly string $path,
        private readonly MigrationOutcome $outcome,
        private readonly string $mediaId = '',
        private readonly string $detail = '',
        private readonly string $key = '',
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
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

    public function withKey(string $key): MediaMigrationResultInterface
    {
        return new self(
            attribute: $this->attribute,
            path: $this->path,
            outcome: $this->outcome,
            mediaId: $this->mediaId,
            detail: $this->detail,
            key: $key,
        );
    }
}
