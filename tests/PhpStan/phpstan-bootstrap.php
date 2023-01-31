<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

class_alias(
    \OxidEsales\Eshop\Application\Controller\TextEditorHandler::class,
    \OxidEsales\WysiwygModule\Application\Controller\TextEditorHandler_parent::class
);

class_alias(
    \OxidEsales\Eshop\Core\Language::class,
    \OxidEsales\WysiwygModule\Core\Language_parent::class
);

class_alias(
    \OxidEsales\Eshop\Core\Utils::class,
    \OxidEsales\WysiwygModule\Core\Utils_parent::class
);

class_alias(
    \OxidEsales\Eshop\Core\ViewConfig::class,
    \OxidEsales\WysiwygModule\Core\ViewConfig_parent::class
);
