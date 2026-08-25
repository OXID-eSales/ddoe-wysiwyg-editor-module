<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

/**
 * The outcome of one media reference found in migrated content, one item of the migration report.
 */
interface MediaMigrationResultInterface
{
    /**
     * The key the migrated content was identified by.
     */
    public function getKey(): string;

    /**
     * The tag attribute the reference was found in, e.g. "src" or "href".
     */
    public function getAttribute(): string;

    /**
     * The media path as it was written in the content before the migration.
     */
    public function getPath(): string;

    public function getOutcome(): MigrationOutcome;

    /**
     * The resolved media id, empty when the reference could not be converted.
     */
    public function getMediaId(): string;

    /**
     * The reason a reference could not be converted, empty on success.
     */
    public function getDetail(): string;
}
