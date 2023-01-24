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

namespace OxidEsales\WysiwygModule\Application\Controller;

use OxidEsales\Eshop\Core\Registry;

/**
 * Class TextEditorHandler
 *
 * @mixin \OxidEsales\Eshop\Application\Controller\TextEditorHandler
 */
class TextEditorHandler extends TextEditorHandler_parent
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
        if (strpos($width, '%') === false) {
            $width .= 'px';
        }

        if (strpos($height, '%') === false) {
            $height .= 'px';
        }

        $oConfig = $this->getConfig();
        $oLang = Registry::getLang();

        $oUtilsView = Registry::get('oxUtilsView');
        $oSmarty = $oUtilsView->getSmarty(true);

        $oSmarty->assign('oView', $this->getView());
        $oSmarty->assign('oViewConf', $this->getViewConfig());

        $oSmarty->assign('sEditorField', $fieldName);
        $oSmarty->assign('sEditorValue', $objectValue);
        $oSmarty->assign('iEditorHeight', $height);
        $oSmarty->assign('iEditorWidth', $width);
        $oSmarty->assign('blTextEditorDisabled', $this->isTextEditorDisabled());

        $iDynInterfaceLanguage = $oConfig->getConfigParam('iDynInterfaceLanguage');
        $sLangAbbr = $oLang->getLanguageAbbr((isset($iDynInterfaceLanguage) ? $iDynInterfaceLanguage : $oLang->getTplLanguage()));

        $oSmarty->assign('langabbr', $sLangAbbr);

        return $oSmarty->fetch('ddoewysiwyg.tpl');
    }

    /**
     * Config instance getter
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    public function getConfig()
    {
        return Registry::getConfig();
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
     * Gets viewConfig object
     *
     * @return object
     */
    public function getViewConfig()
    {
        return $this->getView()->getViewConfig();
    }
}
