<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law.
 *
 * Any unauthorized use of this software will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eSales Visual CMS
 */

namespace OxidEsales\WysiwygModule\Core;

use OxidEsales\WysiwygModule\Application\Model\Media;

/**
 * Class ViewConfig
 *
 * @mixin \OxidEsales\Eshop\Core\ViewConfig
 */
class ViewConfig extends ViewConfig_parent
{

    public function getMediaUrl( $sFile = '' )
    {
        /** @var Media $oMedia */
        $oMedia = oxNew( Media::class );
        return $oMedia->getMediaUrl( $sFile );
    }

}
