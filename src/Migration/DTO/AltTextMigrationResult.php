<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

class AltTextMigrationResult implements AltTextMigrationResultInterface
{
    /**
     * @param string $content The modified content
     * @param CustomAltTextTagInterface[] $customAltTextTags
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

    /** @return CustomAltTextTagInterface[] */
    public function getCustomAltTextTags(): array
    {
        return $this->customAltTextTags;
    }
}
