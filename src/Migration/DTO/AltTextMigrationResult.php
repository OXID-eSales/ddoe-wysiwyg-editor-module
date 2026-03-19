<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

class AltTextMigrationResult
{
    /**
     * @param string $content The modified content
     * @param array<int, array{tag: string, mediaId: string, altText: string}> $customAltTextTags
     */
    public function __construct(
        private readonly string $content,
        private readonly array $customAltTextTags = [],
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /** @return array<int, array{tag: string, mediaId: string, altText: string}> */
    public function getCustomAltTextTags(): array
    {
        return $this->customAltTextTags;
    }
}
