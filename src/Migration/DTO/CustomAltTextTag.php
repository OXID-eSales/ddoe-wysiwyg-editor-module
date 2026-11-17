<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

final class CustomAltTextTag implements CustomAltTextTagInterface
{
    public function __construct(
        private readonly string $key,
        private readonly string $tag,
        private readonly string $mediaId,
        private readonly string $altText,
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function getAltText(): string
    {
        return $this->altText;
    }
}
