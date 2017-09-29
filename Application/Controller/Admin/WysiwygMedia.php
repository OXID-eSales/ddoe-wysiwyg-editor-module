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

namespace OxidEsales\WysiwygModule\Application\Controller\Admin;

use OxidEsales\WysiwygModule\Application\Model\Media;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Core\DatabaseProvider;

/**
 * Class WysiwygMedia
 */
class WysiwygMedia extends AdminDetailsController
{

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'dialog/ddoewysiwygmedia.tpl';

    /**
     * @var Media
     */
    protected $_oMedia = null;

    protected $_sUploadDir = '';
    protected $_sThumbDir = '';
    protected $_iDefaultThumbnailSize = 0;


    /**
     * Overrides oxAdminDetails::init()
     */
    public function init()
    {
        parent::init();

        if ( $this->_oMedia === null )
        {
            if ( class_exists( '\\OxidEsales\\VisualCmsModule\\Application\\Model\\Media' ) )
            {
                $this->_oMedia = oxNew( \OxidEsales\VisualCmsModule\Application\Model\Media::class );
            }
            else
            {
                $this->_oMedia = oxNew( Media::class );
            }
        }

        $this->_sUploadDir = $this->_oMedia->getMediaPath();
        $this->_sThumbDir = $this->_oMedia->getMediaPath();
        $this->_iDefaultThumbnailSize = $this->_oMedia->getDefaultThumbSize();
    }

    /**
     * Overrides oxAdminDetails::render
     *
     * @return string
     */
    public function render()
    {
        $oConfig = $this->getConfig();
        $iShopId = $oConfig->getConfigParam('blMediaLibraryMultiShopCapability') ? $oConfig->getActiveShop()->getShopId() : null;

        $this->_aViewData['aFiles'] = $this->_getFiles(0, $iShopId);
        $this->_aViewData['iFileCount'] = $this->_getFileCount($iShopId);
        $this->_aViewData['sResourceUrl'] = $this->_oMedia->getMediaUrl();
        $this->_aViewData['sThumbsUrl'] = $this->_oMedia->getThumbnailUrl();

        return parent::render();
    }

    /**
     * @param int  $iStart
     * @param null $iShopId
     *
     * @return array
     */
    protected function _getFiles($iStart = 0, $iShopId = null)
    {
        $sSelect = "SELECT * FROM `ddmedia` WHERE 1 " . ($iShopId != null ? "AND `OXSHOPID` = '" . $iShopId . "' " : "") . "ORDER BY `OXTIMESTAMP` DESC LIMIT " . $iStart . ", 18 ";

        return DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sSelect);
    }

    /**
     * @param null $iShopId
     *
     * @return false|string
     */
    protected function _getFileCount($iShopId = null)
    {
        $sSelect = "SELECT COUNT(*) AS 'count' FROM `ddmedia` WHERE 1 " . ($iShopId != null ? "AND `OXSHOPID` = '" . $iShopId . "' " : "");

        return DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getOne($sSelect);
    }

    /**
     * Upload files
     */
    public function upload()
    {
        $oConfig = $this->getConfig();

        $sId = null;

        if ($_FILES) {
            $this->_oMedia->createDirs();

            $sFileSize = $_FILES['file']['size'];
            $sFileType = $_FILES['file']['type'];

            $sSourcePath = $_FILES['file']['tmp_name'];
            $sDestPath = $this->_sUploadDir . $_FILES['file']['name'];

            $aFile = $this->_oMedia->uploadeMedia($sSourcePath, $sDestPath, true);

            $sId = md5( $aFile[ 'filename' ] );
            $sThumbName = $aFile[ 'thumbnail' ];
            $sFileName = $aFile[ 'filename' ];

            $aImageSize = null;
            $sImageSize = '';

            if (is_readable($sDestPath) && preg_match("/image\//", $sFileType)) {
                $aImageSize = getimagesize($sDestPath);
                $sImageSize = ($aImageSize ? $aImageSize[0] . 'x' . $aImageSize[1] : '');
            }

            $iShopId = $oConfig->getActiveShop()->getShopId();

            $sInsert = "REPLACE INTO `ddmedia`
                          ( `OXID`, `OXSHOPID`, `DDFILENAME`, `DDFILESIZE`, `DDFILETYPE`, `DDTHUMB`, `DDIMAGESIZE` )
                        VALUES
                          ( '" . $sId . "', '" . $iShopId . "', '" . $sFileName . "', " . $sFileSize . ", '" . $sFileType . "', '" . $sThumbName . "', '" . $sImageSize . "' );";

            DatabaseProvider::getDb()->execute($sInsert);
        }

        if ($oConfig->getRequestParameter('src') == 'fallback') {
            $this->fallback(true);
        } else {
            header('Content-Type: application/json');
            die(json_encode(array('success' => true, 'id' => $sId, 'file' => $sFileName, 'filepath' => $sDestPath, 'filetype' => $sFileType, 'filesize' => $sFileSize, 'imagesize' => $sImageSize)));
        }
    }

    /**
     * @param bool $blComplete
     * @param bool $blError
     */
    public function fallback($blComplete = false, $blError = false)
    {
        $oViewConf = $this->getViewConfig();

        $sFormHTML = '<html><head></head><body style="text-align:center;">
                          <form action="' . $oViewConf->getSelfLink() . 'cl=ddoewysiwygmedia_view&fnc=upload&src=fallback" method="post" enctype="multipart/form-data">
                              <input type="file" name="file" onchange="this.form.submit();" />
                          </form>';

        if ($blComplete) {
            $sFormHTML .= '<script>window.parent.MediaLibrary.refreshMedia();</script>';
        }

        $sFormHTML .= '</body></html>';

        header('Content-Type: text/html');
        die($sFormHTML);
    }

    /**
     * Remove file
     */
    public function remove()
    {
        $oConfig = $this->getConfig();

        if ($aIDs = $oConfig->getRequestParameter('id')) {
            $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);

            $sSelect = "SELECT `OXID`, `DDFILENAME`, `DDTHUMB` FROM `ddmedia` WHERE `OXID` IN('" . implode("','", $aIDs) . "'); ";
            $aData = $oDb->getAll($sSelect);

            foreach ($aData as $aRow) {
                @unlink($this->_sUploadDir . $aRow['DDFILENAME']);

                if ($aRow['DDTHUMB']) {
                    foreach (glob($this->_sThumbDir . str_replace('thumb_' . $this->_iDefaultThumbnailSize . '.jpg', '*', $aRow['DDTHUMB'])) as $sThumb) {
                        @unlink($sThumb);
                    }
                }

                $sDelete = "DELETE FROM `ddmedia` WHERE `OXID` = '" . $aRow['OXID'] . "'; ";
                $oDb->execute($sDelete);
            }
        }

        exit();
    }

    /**
     * Load more files
     */
    public function moreFiles()
    {
        $oConfig = $this->getConfig();
        $iStart = $oConfig->getRequestParameter('start') ? $oConfig->getRequestParameter('start') : 0;
        //$iShopId = $oConfig->getRequestParameter( 'oxshopid' ) ? $oConfig->getRequestParameter( 'oxshopid' ) : null;
        $iShopId = $oConfig->getConfigParam('blMediaLibraryMultiShopCapability') ? $oConfig->getActiveShop()->getShopId() : null;

        $aFiles = $this->_getFiles($iStart, $iShopId);
        $blLoadMore = ($iStart + 18 < $this->_getFileCount($iShopId));

        header('Content-Type: application/json');
        die(json_encode(array('files' => $aFiles, 'more' => $blLoadMore)));
    }
}
