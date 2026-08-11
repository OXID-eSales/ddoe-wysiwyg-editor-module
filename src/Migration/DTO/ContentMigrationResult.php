<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

class ContentMigrationResult implements ContentMigrationResultInterface
{
    /**
     * @param MediaReferenceResultInterface[] $references
     */
    public function __construct(
        private readonly string $content,
        private readonly array $references,
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getReferences(): array
    {
        return $this->references;
    }
}
