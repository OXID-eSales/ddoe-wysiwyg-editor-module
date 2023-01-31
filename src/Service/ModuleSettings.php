<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;
use OxidEsales\VisualCmsModule\Core\Module;

/**
 * @extendable-class
 */
class ModuleSettings
{
    /** Other Settings */
    public const WYSIWYG_ALTERNATIVE_IMAGE_DIRECTORY = 'ddoeWysiwygAlternativeImageDirectory';

    public function __construct(
        private ModuleSettingServiceInterface $moduleSettingService
    ) {
    }

    /**
     * Other Settings
     */

    public function getAlternativeImageDirectory(): string
    {
        return $this->getStringSettingValue(self::VCMS_ALTERNATIVE_IMAGE_DIRECTORY);
    }

    protected function getStringSettingValue($key): string
    {
        return $this->moduleSettingService->getString(
            $key,
            Module::MODULE_ID
        )->trim()->toString();
    }
}
