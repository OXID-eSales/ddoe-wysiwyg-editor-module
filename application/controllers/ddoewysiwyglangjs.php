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
 * view class
 *
 * @todo    Dokumentation
 * @package views
 */
class ddoewysiwyglangjs extends oxUBase
{

    /**
     * Init function
     */
    public function init()
    {
        /** @var oxLang $oLang */
        $oLang = oxNew(\OxidEsales\Eshop\Core\Language::class);

        header('Content-Type: application/javascript');

        $oUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
        $sJson = $oUtils->encodeJson($oLang->getLanguageStrings());
        $oUtils->showMessageAndExit(";( function(g){ g.i18n = " . $sJson . "; })(window);");
    }
}
