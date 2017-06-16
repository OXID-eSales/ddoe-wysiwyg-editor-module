<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */
/**
 * view class
 *
 * @todo    Dokumentation
 * @package views
 */
class ddoewysiwyglangjs extends oxUBase
{
    public function init()
    {
        /** @var oxLang $oLang */
        $oLang = oxNew( \OxidEsales\Eshop\Core\Language::class );

        header( 'Content-Type: application/javascript' );

        $oUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
        $sJson = $oUtils->encodeJson( $oLang->getLanguageStrings() );
        $oUtils->showMessageAndExit( ";( function(g){ g.i18n = " . $sJson . "; })(window);" );
    }
}