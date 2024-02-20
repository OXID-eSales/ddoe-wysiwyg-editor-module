<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Service;

use OxidEsales\Eshop\Core\ViewConfig;

interface SettingsInterface
{
    public function isSsl(): bool;

    public function getInterfaceLanguageAbbreviation(): string;

    public function getActiveViewConfig(): ViewConfig;
}