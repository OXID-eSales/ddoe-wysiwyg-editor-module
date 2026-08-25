<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;

class MediaMigrationResultFilter implements MediaMigrationResultFilterInterface
{
    public function filterByOutcome(array $results, MigrationOutcome $outcome): array
    {
        return array_values(array_filter(
            $results,
            static fn(MediaMigrationResultInterface $result): bool => $result->getOutcome() === $outcome
        ));
    }
}
