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
 * Class Utils
 *
 * @mixin \OxidEsales\Eshop\Core\Utils
 */
class Utils extends Utils_parent
{

    /**
     * @param null|mixed $mMsg
     */
    public function showJsonAndExit($mMsg = null)
    {
        $this->showMessageAndExit($this->encodeJson($mMsg));
    }

    /**
     * @param null|mixed $mMsg
     *
     * @return string
     */
    public function encodeJson($mMsg = null)
    {
        if (is_string($mMsg)) {
            if (!$this->isUtfString($mMsg)) {
                $mMsg = utf8_encode($mMsg);
            }
        } else {
            // Typecast for Objects
            if (is_object($mMsg)) {
                $mMsg = ( array ) $mMsg;
            }

            $mMsg = $this->_encodeUtf8Array($mMsg);
        }

        return json_encode($mMsg);
    }

    /**
     * @param string $sString
     *
     * @return bool
     */
    public function isUtfString($sString = '')
    {
        if (is_string($sString) && (function_exists('mb_detect_encoding') && mb_detect_encoding($sString, 'UTF-8', true) !== false)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $aArray
     *
     * @return array
     */
    protected function _encodeUtf8Array($aArray)
    {
        $aRet = array();

        foreach ($aArray as $sKey => $mValue) {
            if (!$this->isUtfString($mValue)) {
                $sKey = utf8_encode($sKey);
            }

            if (is_string($mValue)) {
                if (!$this->isUtfString($mValue)) {
                    $mValue = utf8_encode($mValue);
                }
            } elseif (is_array($mValue)) {
                $mValue = $this->_encodeUtf8Array($mValue);
            }

            $aRet[$sKey] = $mValue;
        }

        return $aRet;
    }
}
