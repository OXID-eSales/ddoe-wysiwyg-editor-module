<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

/**
 * @phpstan-type CustomAltTextTag array{tag: string, mediaId: string, altText: string}
 */
interface AltTextMigrationResultInterface
{
    public function getContent(): string;

    /** @return array<int, CustomAltTextTag> */
    public function getCustomAltTextTags(): array;
}
