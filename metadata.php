<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
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
    'version'     => '1.0.0_beta',
    'author'      => 'OXID eSales AG & digidesk - media solutions',
    'url'         => 'http://www.oxid-esales.com',
    'email'       => 'info@oxid-esales.com',
    'extend'      => array(

        // Admin Controller

        \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController::class => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygoxadmindetails',

        \OxidEsales\Eshop\Application\Controller\Admin\ArticleMain::class    => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygoxadmindetails',
        \OxidEsales\Eshop\Application\Controller\Admin\CategoryText::class   => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygoxadmindetails',
        \OxidEsales\Eshop\Application\Controller\Admin\ContentMain::class    => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygoxadmindetails',
        \OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class    => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygoxadmindetails',
        \OxidEsales\Eshop\Application\Controller\Admin\AdminlinksMain::class => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygoxadmindetails',
        \OxidEsales\Eshop\Application\Controller\Admin\NewsletterMain::class => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygoxadmindetails',
        \OxidEsales\Eshop\Application\Controller\Admin\NewsText::class       => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygoxadmindetails',
        \OxidEsales\Eshop\Application\Controller\Admin\PaymentMain::class    => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygoxadmindetails',
        \OxidEsales\Eshop\Application\Controller\Admin\PriceAlarmMain::class => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygoxadmindetails',

        // Core

        \OxidEsales\Eshop\Core\Language::class => 'ddoe/wysiwyg/core/ddoewysiwygoxlang',
        \OxidEsales\Eshop\Core\Utils::class    => 'ddoe/wysiwyg/core/ddoewysiwygoxutils',

    ),
    'files' => array(

        // Models

        'ddoewysiwygmedia'         => 'ddoe/wysiwyg/application/models/ddoewysiwygmedia.php',

        // Controller

        'ddoewysiwyglangjs'        => 'ddoe/wysiwyg/application/controllers/ddoewysiwyglangjs.php',
        'ddoewysiwygmedia_view'    => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygmedia_view.php',
        'ddoewysiwygmedia_wrapper' => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygmedia_wrapper.php',

        // Events
        'ddoewysiwygevents'        => 'ddoe/wysiwyg/application/events/ddoewysiwygevents.php',

    ),
    'templates' => array(

        'ddoewysiwyg.tpl'                     => 'ddoe/wysiwyg/application/views/admin/tpl/ddoewysiwyg.tpl',
        'dialog/ddoewysiwygmedia.tpl'         => 'ddoe/wysiwyg/application/views/admin/tpl/dialog/ddoewysiwygmedia.tpl',
        'dialog/ddoewysiwygmedia_wrapper.tpl' => 'ddoe/wysiwyg/application/views/admin/tpl/dialog/ddoewysiwygmedia_wrapper.tpl',

    ),
    'events' => array(
        'onActivate'   => 'ddoewysiwygevents::onActivate',
        'onDeactivate' => 'ddoewysiwygevents::onDeactivate'
    ),
    'blocks' => array(
    ),
    'settings' => array(
    )
);
