<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Service;

use OxidEsales\Eshop\Core\Config as ShopConfig;
use OxidEsales\Eshop\Core\Language as ShopLanguage;
use OxidEsales\Eshop\Core\ViewConfig;

class Settings implements SettingsInterface
{
    public function __construct(
        protected ShopConfig $shopConfig,
        protected ShopLanguage $shopLanguage,
    ) {
    }

    public function isSsl(): bool
    {
        return $this->shopConfig->getConfigParam('sSSLShopURL') || $this->shopConfig->getConfigParam('sMallSSLShopURL');
    }

    public function getInterfaceLanguageAbbreviation(): string
    {
        $templateLanguageId = $this->shopLanguage->getTplLanguage();
        return $this->shopLanguage->getLanguageAbbr($templateLanguageId);
    }

    public function getActiveViewConfig(): ViewConfig
    {
        return $this->shopConfig->getTopActiveView()->getViewConfig();
    }
}
