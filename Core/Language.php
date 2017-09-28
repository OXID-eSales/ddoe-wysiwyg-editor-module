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

namespace OxidEsales\WysiwygModule\Core;

/**
 * Class Language
 *
 * @mixin \OxidEsales\Eshop\Core\Language
 */
class Language extends Language_parent
{

    /**
     * @param null|integer $iLang
     * @param null|bool    $blAdminMode
     *
     * @return array
     */
    public function getLanguageStrings($iLang = null, $blAdminMode = null)
    {
        $aLang = array();

        foreach ($this->_getLangTranslationArray($iLang, $blAdminMode) as $sLangKey => $sLangValue) {
            $aLang[$sLangKey] = $sLangValue;
        }

        foreach ($this->_getLanguageMap($iLang, $blAdminMode) as $sLangKey => $sLangValue) {
            $aLang[$sLangKey] = $sLangValue;
        }

        if (count($this->_aAdditionalLangFiles)) {
            foreach ($this->_getLangTranslationArray($iLang, $blAdminMode, $this->_aAdditionalLangFiles) as $sLangKey => $sLangValue) {
                $aLang[$sLangKey] = $sLangValue;
            }
        }

        return $aLang;
    }
}
