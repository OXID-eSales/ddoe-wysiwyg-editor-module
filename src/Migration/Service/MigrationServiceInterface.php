<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Migration\Service;

interface MigrationServiceInterface
{
    public function migrateContent(string $content): string;
}
