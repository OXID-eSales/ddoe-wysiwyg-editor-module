<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
     * @param null|bool $blAdminMode
     *
     * @return array
     */
    public function getLanguageStrings($iLang = null, $blAdminMode = null)
    {
        $aLang = [];

        foreach ($this->getLangTranslationArray($iLang, $blAdminMode) as $sLangKey => $sLangValue) {
            $aLang[$sLangKey] = $sLangValue;
        }

        foreach ($this->getLanguageMap($iLang, $blAdminMode) as $sLangKey => $sLangValue) {
            $aLang[$sLangKey] = $sLangValue;
        }

        if (count($this->_aAdditionalLangFiles)) {
            foreach (
                $this->getLangTranslationArray(
                    $iLang,
                    $blAdminMode,
                    $this->_aAdditionalLangFiles
                ) as $sLangKey => $sLangValue
            ) {
                $aLang[$sLangKey] = $sLangValue;
            }
        }

        return $aLang;
    }
}
