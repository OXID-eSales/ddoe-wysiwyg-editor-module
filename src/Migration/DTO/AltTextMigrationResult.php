<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

/**
 * @phpstan-import-type CustomAltTextTag from AltTextMigrationResultInterface
 */
class AltTextMigrationResult implements AltTextMigrationResultInterface
{
    /**
     * @param string $content The modified content
     * @param array<int, CustomAltTextTag> $customAltTextTags
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

    /** @return array<int, CustomAltTextTag> */
    public function getCustomAltTextTags(): array
    {
        return $this->customAltTextTags;
    }
}
