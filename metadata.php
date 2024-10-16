<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\Eshop\Application\Controller\TextEditorHandler;
use OxidEsales\WysiwygModule\Service\ModuleSettings;

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = [
    'id'          => 'ddoewysiwyg',
    'title'       => 'Summernote WYSIWYG Editor for OXID eShop',
    'description' => [
        'de' => '',
        'en' => '',
    ],
    'thumbnail'   => 'logo.png',
    'version'     => '4.2.0',
    'author'      => 'OXID eSales AG & digidesk - media solutions',
    'url'         => 'https://www.oxid-esales.com',
    'email'       => 'info@oxid-esales.com',
    'extend'      => [
        // Admin Controller
        TextEditorHandler::class => \OxidEsales\WysiwygModule\Application\Controller\TextEditorHandler::class,
    ],
    'events'      => [
        'onActivate'   => '\OxidEsales\WysiwygModule\Core\Events::onActivate',
        'onDeactivate' => '\OxidEsales\WysiwygModule\Core\Events::onDeactivate'
    ],
];
