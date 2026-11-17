<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

interface AltTextMigrationResultInterface
{
    public function getContent(): string;

    /** @return CustomAltTextTagInterface[] */
    public function getCustomAltTextTags(): array;
}
