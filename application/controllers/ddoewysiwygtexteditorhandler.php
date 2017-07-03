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

class ddoewysiwygtexteditorhandler extends ddoewysiwygtexteditorhandler_parent
{

    /**
     * Render text editor.
     *
     * @param int    $width       The editor width
     * @param int    $height      The editor height
     * @param object $objectValue The object value passed to editor
     * @param string $fieldName   The name of object field which content is passed to editor
     *
     * @return string The Editor output
     */
    public function renderRichTextEditor($width, $height, $objectValue, $fieldName)
    {
        if( strpos( $width, '%' ) === false ) {
            $width .= 'px';
        }

        if( strpos( $height, '%' ) === false ) {
            $height .= 'px';
        }

        $oConfig = $this->getConfig();
        $oLang = \OxidEsales\Eshop\Core\Registry::getLang();

        $oUtilsView = \OxidEsales\Eshop\Core\Registry::get( 'oxUtilsView' );
        $oSmarty = $oUtilsView->getSmarty( true );

        $oSmarty->assign( 'oView', $this->getView() );
        $oSmarty->assign( 'oViewConf', $this->getViewConfig() );

        $oSmarty->assign( 'sEditorField', $fieldName );
        $oSmarty->assign( 'sEditorValue', $objectValue );
        $oSmarty->assign( 'iEditorHeight', $height );
        $oSmarty->assign( 'iEditorWidth', $width );

        $iDynInterfaceLanguage = $oConfig->getConfigParam( 'iDynInterfaceLanguage' );
        $sLangAbbr = $oLang->getLanguageAbbr( ( isset( $iDynInterfaceLanguage ) ? $iDynInterfaceLanguage : $oLang->getTplLanguage() ) );

        $oSmarty->assign( 'langabbr', $sLangAbbr );

        return $oSmarty->fetch( 'ddoewysiwyg.tpl' );
    }

    /**
     * Gets viewConfig object
     *
     * @return object
     */
    public function getViewConfig()
    {
        return $this->getView()->getViewConfig();
    }

    /**
     * Get active view
     *
     * @return object
     */
    public function getView()
    {
        return $this->getConfig()->getTopActiveView();
    }

    /**
     * Config instance getter
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    public function getConfig()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig();
    }
}