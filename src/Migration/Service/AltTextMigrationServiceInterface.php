<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\AltTextMigrationResult;

interface AltTextMigrationServiceInterface
{
    public function migrateAltTexts(string $content): AltTextMigrationResult;
}
