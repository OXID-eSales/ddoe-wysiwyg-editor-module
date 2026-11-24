<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\Repository;

use OxidEsales\WysiwygModule\Migration\DTO\CustomAltTextTagInterface;
use OxidEsales\WysiwygModule\Migration\DTO\TableFieldInterface;

interface AltTextMigrationRepositoryInterface
{
    /**
     * Migrates the alt texts of one table field.
     *
     * @return CustomAltTextTagInterface[] media images with a custom alt text, which were left untouched
     */
    public function migrateTableField(TableFieldInterface $tableField): array;
}
