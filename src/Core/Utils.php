<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
                $mMsg = (array)$mMsg;
            }

            $mMsg = $this->encodeUtf8Array($mMsg);
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
        if (
            is_string($sString)
            && (function_exists('mb_detect_encoding')
                && mb_detect_encoding($sString, 'UTF-8', true) !== false)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param array $aArray
     *
     * @return array
     */
    protected function encodeUtf8Array($aArray)
    {
        $aRet = [];

        foreach ($aArray as $sKey => $mValue) {
            if (!$this->isUtfString($mValue)) {
                $sKey = utf8_encode($sKey);
            }

            if (is_string($mValue)) {
                if (!$this->isUtfString($mValue)) {
                    $mValue = utf8_encode($mValue);
                }
            } elseif (is_array($mValue)) {
                $mValue = $this->encodeUtf8Array($mValue);
            }

            $aRet[$sKey] = $mValue;
        }

        return $aRet;
    }
}