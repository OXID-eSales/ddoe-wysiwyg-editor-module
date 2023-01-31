<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'ddoewysiwyg',
    'title'       => 'WYSIWYG Editor + Mediathek',
    'description' => array(
        'de' => '',
        'en' => '',
    ),
    'thumbnail'   => 'logo.png',
    'version'     => '2.4.1',
    'author'      => 'OXID eSales AG & digidesk - media solutions',
    'url'         => 'http://www.oxid-esales.com',
    'email'       => 'info@oxid-esales.com',
    'extend'      => array(

        // Admin Controller
        \OxidEsales\Eshop\Application\Controller\TextEditorHandler::class => \OxidEsales\WysiwygModule\Application\Controller\TextEditorHandler::class,

        // Core
        \OxidEsales\Eshop\Core\ViewConfig::class  => \OxidEsales\WysiwygModule\Core\ViewConfig::class,
        \OxidEsales\Eshop\Core\Language::class    => \OxidEsales\WysiwygModule\Core\Language::class,
        \OxidEsales\Eshop\Core\Utils::class       => \OxidEsales\WysiwygModule\Core\Utils::class,

    ),
    'controllers'       => array(

        // Lang
        'ddoewysiwyglangjs' => \OxidEsales\WysiwygModule\Application\Controller\WysiwygLangJs::class,

        // Admin Controller
        'ddoewysiwygmedia_view'    => \OxidEsales\WysiwygModule\Application\Controller\Admin\WysiwygMedia::class,
        'ddoewysiwygmedia_wrapper' => \OxidEsales\WysiwygModule\Application\Controller\Admin\WysiwygMediaWrapper::class,

    ),
    'templates'   => array(
        '@ddoewysiwyg/ddoewysiwyg.tpl'                     => 'views/admin_smarty/tpl/ddoewysiwyg.tpl',
        '@ddoewysiwyg/dialog/ddoewysiwygmedia.tpl'         => 'views/admin_smarty/tpl/dialog/ddoewysiwygmedia.tpl',
        '@ddoewysiwyg/dialog/ddoewysiwygmedia_wrapper.tpl' => 'views/admin_smarty/tpl/dialog/ddoewysiwygmedia_wrapper.tpl',
    ),
    'events'      => array(
        'onActivate'   => '\OxidEsales\WysiwygModule\Core\Events::onActivate',
        'onDeactivate' => '\OxidEsales\WysiwygModule\Core\Events::onDeactivate'
    ),
    'blocks'      => array(),
    'settings'    => array()
);
