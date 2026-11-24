<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\DTO;

interface AltTextMigrationResultInterface
{
    public function getContent(): string;

    /** @return CustomAltTextTagInterface[] */
    public function getCustomAltTextTags(): array;
}
