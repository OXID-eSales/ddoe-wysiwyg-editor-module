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

        'oxadmindetails' => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygoxadmindetails',

        'article_main' => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygoxadmindetails',

    ),
    'files' => array(

        'ddoewysiwyglangjs' => 'ddoe/wysiwyg/application/controllers/ddoewysiwyglangjs.php',
        'ddoewysiwygmedia'  => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygmedia.php',
        'ddoewysiwygmedia_wrapper'  => 'ddoe/wysiwyg/application/controllers/admin/ddoewysiwygmedia_wrapper.php',

    ),
    'templates' => array(

        'ddoewysiwyg.tpl' => 'ddoe/wysiwyg/application/views/admin/tpl/ddoewysiwyg.tpl',
        'dialog/ddoewysiwygmedia.tpl' => 'ddoe/wysiwyg/application/views/admin/tpl/dialog/ddoewysiwygmedia.tpl',
        'dialog/ddoewysiwygmedia_wrapper.tpl' => 'ddoe/wysiwyg/application/views/admin/tpl/dialog/ddoewysiwygmedia_wrapper.tpl',

    ),
    'blocks' => array(
    ),
    'events' => array(
    ),
    'settings' => array(
    )
);
