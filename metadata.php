<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law.
 *
 * Any unauthorized use of this software will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eSales Summernote
 */

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

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
    'version'     => '1.0.0',
    'author'      => 'OXID eSales AG & digidesk - media solutions',
    'url'         => 'http://www.oxid-esales.com',
    'email'       => 'info@oxid-esales.com',
    'extend'      => array(

        // Admin Controller

        \OxidEsales\Eshop\Application\Controller\TextEditorHandler::class => 'ddoe/wysiwyg/application/controllers/ddoewysiwygtexteditorhandler',

        // Core

        \OxidEsales\Eshop\Core\Language::class => 'ddoe/wysiwyg/core/ddoewysiwygoxlang',
        \OxidEsales\Eshop\Core\Utils::class    => 'ddoe/wysiwyg/core/ddoewysiwygoxutils',

    ),
    'files'       => array(

        // Models

        'ddoewysiwygmedia' => 'ddoe/wysiwyg/application/models/ddoewysiwygmedia.php',

        // Controller

        'ddoewysiwyglangjs'        => 'ddoe/wysiwyg/application/controllers/ddoewysiwyglangjs.php',
        'ddoewysiwygmedia_view'    => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygmedia_view.php',
        'ddoewysiwygmedia_wrapper' => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygmedia_wrapper.php',

        // Events
        'ddoewysiwygevents'        => 'ddoe/wysiwyg/application/events/ddoewysiwygevents.php',

    ),
    'templates'   => array(

        'ddoewysiwyg.tpl'                     => 'ddoe/wysiwyg/application/views/admin/tpl/ddoewysiwyg.tpl',
        'dialog/ddoewysiwygmedia.tpl'         => 'ddoe/wysiwyg/application/views/admin/tpl/dialog/ddoewysiwygmedia.tpl',
        'dialog/ddoewysiwygmedia_wrapper.tpl' => 'ddoe/wysiwyg/application/views/admin/tpl/dialog/ddoewysiwygmedia_wrapper.tpl',

    ),
    'events'      => array(
        'onActivate'   => 'ddoewysiwygevents::onActivate',
        'onDeactivate' => 'ddoewysiwygevents::onDeactivate'
    ),
    'blocks'      => array(),
    'settings'    => array()
);
