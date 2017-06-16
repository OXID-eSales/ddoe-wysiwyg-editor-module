<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */
    
/**
 * @package core
 */
class ddoewysiwygoxlang extends ddoewysiwygoxlang_parent
{
    public function getLanguageStrings( $iLang = null, $blAdminMode = null )
    {
        $aLang = array();

        foreach( $this->_getLangTranslationArray( $iLang, $blAdminMode ) as $sLangKey => $sLangValue )
        {
            $aLang[ $sLangKey ] = $sLangValue;
        }

        foreach( $this->_getLanguageMap( $iLang, $blAdminMode ) as $sLangKey => $sLangValue )
        {
            $aLang[ $sLangKey ] = $sLangValue;
        }

        if( count( $this->_aAdditionalLangFiles ) )
        {
            foreach( $this->_getLangTranslationArray( $iLang, $blAdminMode, $this->_aAdditionalLangFiles ) as $sLangKey => $sLangValue )
            {
                $aLang[ $sLangKey ] = $sLangValue;
            }
        }

        return $aLang;
    }
}