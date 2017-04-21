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
        $oLang = oxNew( 'oxlang' );

        header( 'Content-Type: application/javascript' );

        $oUtils = oxRegistry::getUtils();
        $sJson = $oUtils->encodeJson( $oLang->getLanguageStrings() );
        $oUtils->showMessageAndExit( ";( function(g){ g.i18n = " . $sJson . "; })(window);" );
    }
}