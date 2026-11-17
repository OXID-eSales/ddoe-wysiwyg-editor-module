<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\AltTextMigrationResultInterface;

interface AltTextMigrationServiceInterface
{
    /**
     * Adds alt text placeholders to the media images of the given content, reporting the images
     * with a custom alt text it left untouched.
     *
     * @param string $key Key of the row the content belongs to, carried into the reported tags
     */
    public function migrateAltTexts(string $content, string $key = ''): AltTextMigrationResultInterface;
}
