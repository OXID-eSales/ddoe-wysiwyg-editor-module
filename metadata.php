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
    'title'       => 'WYSIWYG Editor + Mediathek',
    'description' => [
        'de' => '',
        'en' => '',
    ],
    'thumbnail'   => 'logo.png',
    'version'     => '3.0.1',
    'author'      => 'OXID eSales AG & digidesk - media solutions',
    'url'         => 'https://www.oxid-esales.com',
    'email'       => 'info@oxid-esales.com',
    'extend'      => [

        // Admin Controller
        TextEditorHandler::class => \OxidEsales\WysiwygModule\Application\Controller\TextEditorHandler::class,

        // Core
        // todo: is the same as in media library module. Should be optimized
        \OxidEsales\Eshop\Core\Language::class    => \OxidEsales\WysiwygModule\Core\Language::class,
        // todo: is almost the same as in media library module. Should be optimized
        \OxidEsales\Eshop\Core\Utils::class       => \OxidEsales\WysiwygModule\Core\Utils::class,
    ],
    'controllers'       => [
        // Lang
        'ddoewysiwyglangjs' => \OxidEsales\WysiwygModule\Application\Controller\WysiwygLangJs::class,
    ],
    'events'      => [
        'onActivate'   => '\OxidEsales\WysiwygModule\Core\Events::onActivate',
        'onDeactivate' => '\OxidEsales\WysiwygModule\Core\Events::onDeactivate'
    ],
];
