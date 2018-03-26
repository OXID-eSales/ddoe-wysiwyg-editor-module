<?php
/**
 * This file is part of OXID eSales WYSIWYG module.
 *
 * OXID eSales WYSIWYG module is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales WYSIWYG module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales WYSIWYG module.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eSales WYSIWYG
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
    'version'     => '2.1.1',
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

        'ddoewysiwyg.tpl'                     => 'ddoe/wysiwyg/Application/views/admin/tpl/ddoewysiwyg.tpl',
        'dialog/ddoewysiwygmedia.tpl'         => 'ddoe/wysiwyg/Application/views/admin/tpl/dialog/ddoewysiwygmedia.tpl',
        'dialog/ddoewysiwygmedia_wrapper.tpl' => 'ddoe/wysiwyg/Application/views/admin/tpl/dialog/ddoewysiwygmedia_wrapper.tpl',

    ),
    'events'      => array(
        'onActivate'   => '\OxidEsales\WysiwygModule\Core\Events::onActivate',
        'onDeactivate' => '\OxidEsales\WysiwygModule\Core\Events::onDeactivate'
    ),
    'blocks'      => array(),
    'settings'    => array()
);
