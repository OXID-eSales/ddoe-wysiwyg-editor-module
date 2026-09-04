<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;

interface MediaMigrationResultFilterInterface
{
    /**
     * @param MediaMigrationResultInterface[] $results
     *
     * @return MediaMigrationResultInterface[]
     */
    public function filterByOutcome(array $results, MigrationOutcome $outcome): array;
}
