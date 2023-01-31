<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Application\Model;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class Content
 *
 * @mixin \OxidEsales\Eshop\Application\Model\Content
 * @mixin \OxidEsales\Eshop\Core\Model\BaseModel
 */
class Content extends Content_parent
{
    /**
     * @param $sName
     * @return bool
     *
     * @throws DatabaseConnectionException
     */
    public function checkIfMediaFileOrFolderIsInUse($sName): bool
    {
        $oDb = DatabaseProvider::getDb();

        $aLangs = \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageArray();
        $aWheres = [];
        foreach ($aLangs as $oLang) {
            if ($oLang->id == 0) {
                $aWheres[] = "`OXCONTENT` LIKE '%$sName%'";
            } else {
                $aWheres[] = "`OXCONTENT_{$oLang->id}` LIKE '%$sName%'";
            }
        }

        $sSelect = "SELECT COUNT(*) FROM `oxcontents` WHERE " . implode(' OR ', $aWheres);

        $blFileInUse = (bool)$oDb->getOne($sSelect);

        return $blFileInUse;
    }
}
