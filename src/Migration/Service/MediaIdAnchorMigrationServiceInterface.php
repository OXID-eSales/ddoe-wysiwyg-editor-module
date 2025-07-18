<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\Service;

interface MediaIdAnchorMigrationServiceInterface
{
    public function migrateToMediaIdAnchors(string $content): string;
}
