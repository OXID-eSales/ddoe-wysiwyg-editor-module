<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\DTO;

interface ContentMigrationResultInterface
{
    /**
     * The migrated content, unchanged where nothing could be converted.
     */
    public function getContent(): string;

    /**
     * Every media reference the migration came across, converted or not.
     *
     * @return MediaMigrationResultInterface[]
     */
    public function getReferences(): array;
}
