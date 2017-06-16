<?php

/**
 *
 *     |o     o    |          |
 * ,---|.,---..,---|,---.,---.|__/
 * |   |||   |||   ||---'`---.|  \
 * `---'``---|``---'`---'`---'`   `
 *       `---'    [media solutions]
 *
 * @copyright   (c) digidesk - media solutions
 * @link        http://www.digidesk.de
 * @author      pclaisse
 */

class ddoewysiwygoxadmindetails extends ddoewysiwygoxadmindetails_parent
{

    /**
     * Generates Text editor html code
     *
     * @param int    $iWidth      editor width
     * @param int    $iHeight     editor height
     * @param object $oObject     object passed to editor
     * @param string $sField      object field which content is passed to editor
     * @param string $sStylesheet stylesheet to use in editor
     *
     * @return string Editor output
     */
    protected function _generateTextEditor( $iWidth, $iHeight, $oObject, $sField, $sStylesheet = null )
    {
        $sEditObjectValue = $this->_getEditValue( $oObject, $sField );

        if( strpos( $iWidth, '%' ) === false )
        {
            $iWidth .= 'px';
        }

        if( strpos( $iHeight, '%' ) === false )
        {
            $iHeight .= 'px';
        }

        $oConfig = $this->getConfig();
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();

        $oUtilsView = \OxidEsales\Eshop\Core\Registry::get( 'oxUtilsView' );
        $oSmarty = $oUtilsView->getSmarty( true );

        $oSmarty->assign( 'oView', $this );
        $oSmarty->assign( 'oViewConf', $this->getViewConfig() );

        $oSmarty->assign( 'sEditorField', $sField );
        $oSmarty->assign( 'sEditorValue', $sEditObjectValue );
        $oSmarty->assign( 'iEditorHeight', $iHeight );
        $oSmarty->assign( 'iEditorWidth', $iWidth );

        $iDynInterfaceLanguage = $oConfig->getConfigParam( 'iDynInterfaceLanguage' );
        $sLangAbbr = $oLang->getLanguageAbbr( ( isset( $iDynInterfaceLanguage ) ? $iDynInterfaceLanguage : $oLang->getTplLanguage() ) );

        $oSmarty->assign( 'langabbr', $sLangAbbr );

        return $oSmarty->fetch( 'ddoewysiwyg.tpl' );
    }

}