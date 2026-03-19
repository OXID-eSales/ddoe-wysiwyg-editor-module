<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Repository;

interface AltTextMigrationRepositoryInterface
{
    /**
     * @return array<int, array{key: string, tag: string, mediaId: string, altText: string}>
     */
    public function migrateTableField(string $tableName, string $fieldName, string $tableKey): array;
}
