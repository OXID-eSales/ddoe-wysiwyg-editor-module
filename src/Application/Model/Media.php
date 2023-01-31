<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Application\Model;

use DirectoryIterator;
use Exception;
use OxidEsales\Eshop\Core\Model\BaseModel;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class Media
 *
 * @mixin BaseModel
 */
class Media extends BaseModel
{
    protected $_sMediaPath = '/out/pictures/ddmedia/';
    protected $_iDefaultThumbnailSize = 185;
    protected $_aFileExtBlacklist = [
        'php.*',
        'exe',
        'js',
        'jsp',
        'cgi',
        'cmf',
        'phtml',
        'pht',
        'phar'
    ]; // regex allowed


    /**
     * @param null|string $sTableName
     * @param bool $blForceAllFields
     */
    public function init($sTableName = null, $blForceAllFields = false)
    {
    }

    /**
     * @param string $sFile
     * @param null|integer $iThumbSize
     *
     * @return bool|string
     */
    public function getThumbnailUrl($sFile = '', $iThumbSize = null)
    {
        if ($sFile) {
            if (!$iThumbSize) {
                $iThumbSize = $this->_iDefaultThumbnailSize;
            }

            $sThumbName = $this->getThumbName($sFile, $iThumbSize);

            if ($sThumbName) {
                return $this->getMediaUrl('thumbs/' . $sThumbName);
            }
        } else {
            return $this->getMediaUrl('thumbs/');
        }

        return false;
    }

    /**
     * @param string $sFile
     * @param null|integer $iThumbSize
     *
     * @return string
     */
    public function getThumbName($sFile, $iThumbSize = null)
    {
        if (!$iThumbSize) {
            $iThumbSize = $this->_iDefaultThumbnailSize;
        }

        return str_replace('.', '_', md5(basename($sFile))) . '_thumb_' . $iThumbSize . '.jpg';
    }

    /**
     * @param string $sFile
     *
     * @return bool|string
     */
    public function getMediaUrl($sFile = '')
    {
        $oConfig = Registry::getConfig();

        $sFilePath = $this->getMediaPath($sFile);

        if (!is_readable($sFilePath)) {
            return false;
        }

        if ($oConfig->isSsl()) {
            $sUrl = $oConfig->getSslShopUrl(false);
        } else {
            $sUrl = $oConfig->getShopUrl(false);
        }

        $sUrl = rtrim($sUrl, '/') . $this->_sMediaPath;

        if ($sFile) {
            return $sUrl . $sFile;
        }

        return $sUrl;
    }

    /**
     * @param string $sFile
     *
     * @return string
     */
    public function getMediaPath($sFile = '')
    {
        $sPath = rtrim(getShopBasePath(), '/') . $this->_sMediaPath;

        if ($sFile) {
            return $sPath . $sFile;
        }

        return $sPath;
    }

    /**
     * @return int
     */
    public function getDefaultThumbSize()
    {
        return $this->_iDefaultThumbnailSize;
    }

    /**
     * @param string $sSourcePath
     * @param string $sDestPath
     * @param bool $blCreateThumbs
     *
     * @return array
     */
    public function uploadeMedia($sSourcePath, $sDestPath, $blCreateThumbs = false)
    {
        $this->createDirs();

        $sThumbName = '';
        $sFileName = basename($sDestPath);
        $iFileCount = 0;

        if ($this->validateFilename($sFileName)) {
            while (file_exists($sDestPath)) {
                $aFileParts = explode('.', $sFileName);
                $aFileParts = array_reverse($aFileParts);

                $sFileExt = $aFileParts[0];
                unset($aFileParts[0]);

                $sBaseName = implode('.', array_reverse($aFileParts));

                $aBaseParts = explode('_', $sBaseName);
                $aBaseParts = array_reverse($aBaseParts);

                if (strlen($aBaseParts[0]) == 1 && is_numeric($aBaseParts[0])) {
                    $iFileCount = (int)$aBaseParts[0];
                    unset($aBaseParts[0]);
                }

                $sBaseName = implode('_', array_reverse($aBaseParts));

                $sFileName = $sBaseName . '_' . (++$iFileCount) . '.' . $sFileExt;
                $sDestPath = dirname($sDestPath) . '/' . $sFileName;
            }

            move_uploaded_file($sSourcePath, $sDestPath);

            if ($blCreateThumbs) {
                try {
                    $sThumbName = $this->createThumbnail($sFileName);

                    $this->createMoreThumbnails($sFileName);
                } catch (Exception $e) {
                    $sThumbName = '';
                }
            }

            return [
                'filepath' => $sDestPath,
                'filename' => $sFileName,
                'thumbnail' => $sThumbName
            ];
        }

        return false;
    }

    /**
     * Create directories
     */
    public function createDirs()
    {
        if (!is_dir($this->getMediaPath())) {
            mkdir($this->getMediaPath());
        }

        if (!is_dir($this->getThumbnailPath())) {
            mkdir($this->getThumbnailPath());
        }
    }

    /**
     * @param string $sFile
     *
     * @return string
     */
    public function getThumbnailPath($sFile = '')
    {
        $sPath = $this->getMediaPath() . 'thumbs/';

        if ($sFile) {
            return $sPath . $sFile;
        }

        return $sPath;
    }

    /**
     * @param string $sFileName
     * @param null|integer $iThumbSize
     * @param bool $blCrop
     *
     * @return bool|string
     * @throws Exception
     */
    public function createThumbnail($sFileName, $iThumbSize = null, $blCrop = true)
    {
        $sFilePath = $this->getMediaPath($sFileName);

        if (is_readable($sFilePath)) {
            if (!$iThumbSize) {
                $iThumbSize = $this->_iDefaultThumbnailSize;
            }

            list($iImageWidth, $iImageHeight, $iImageType) = getimagesize($sFilePath);

            switch ($iImageType) {
                case 1:
                    $rImg = imagecreatefromgif($sFilePath);
                    break;

                case 2:
                    $rImg = imagecreatefromjpeg($sFilePath);
                    break;

                case 3:
                    $rImg = imagecreatefrompng($sFilePath);
                    break;

                default:
                    throw new Exception('Invalid filetype');
                    break;
            }

            $iThumbWidth = $iImageWidth;
            $iThumbHeight = $iImageHeight;

            $iThumbX = 0;
            $iThumbY = 0;

            if ($blCrop) {
                if ($iImageWidth < $iImageHeight) {
                    $iThumbWidth = $iThumbSize;
                    $iThumbHeight = $iImageHeight / ($iImageWidth / $iThumbWidth);

                    $iThumbY = (($iThumbSize - $iThumbHeight) / 2);
                } elseif ($iImageHeight < $iImageWidth) {
                    $iThumbHeight = $iThumbSize;
                    $iThumbWidth = $iImageWidth / ($iImageHeight / $iThumbHeight);

                    $iThumbX = (($iThumbSize - $iThumbWidth) / 2);
                }
            } else {
                if ($iImageWidth < $iImageHeight) {
                    if ($iImageHeight > $iThumbSize) {
                        $iThumbWidth *= ($iThumbSize / $iImageHeight);
                        $iThumbHeight *= ($iThumbSize / $iImageHeight);
                    }
                } elseif ($iImageHeight < $iImageWidth) {
                    if ($iImageHeight > $iThumbSize) {
                        $iThumbWidth *= ($iThumbSize / $iImageWidth);
                        $iThumbHeight *= ($iThumbSize / $iImageWidth);
                    }
                }
            }

            $rTmpImg = imagecreatetruecolor($iThumbWidth, $iThumbHeight);
            imagecopyresampled(
                $rTmpImg,
                $rImg,
                $iThumbX,
                $iThumbY,
                0,
                0,
                $iThumbWidth,
                $iThumbHeight,
                $iImageWidth,
                $iImageHeight
            );

            if ($blCrop) {
                $rThumbImg = imagecreatetruecolor($iThumbSize, $iThumbSize);
                imagefill($rThumbImg, 0, 0, imagecolorallocate($rThumbImg, 0, 0, 0));

                imagecopymerge($rThumbImg, $rTmpImg, 0, 0, 0, 0, $iThumbSize, $iThumbSize, 100);
            } else {
                $rThumbImg = $rTmpImg;
            }

            $sThumbName = $this->getThumbName($sFileName, $iThumbSize);

            imagejpeg($rThumbImg, $this->getThumbnailPath($sThumbName));

            return $sThumbName;
        }

        return false;
    }

    /**
     * @param string $sFileName
     */
    public function createMoreThumbnails($sFileName)
    {
        // More Thumbnail Sizes
        $this->createThumbnail($sFileName, 300);
        $this->createThumbnail($sFileName, 800);
    }

    /**
     * @param null|integer $iThumbSize
     * @param bool $blOverwrite
     * @param bool $blCrop
     */
    public function generateThumbnails($iThumbSize = null, $blOverwrite = false, $blCrop = true)
    {
        if (!$iThumbSize) {
            $iThumbSize = $this->_iDefaultThumbnailSize;
        }

        if (is_dir($this->getMediaPath())) {
            foreach (new DirectoryIterator($this->getMediaPath()) as $oFile) {
                if ($oFile->isFile()) {
                    $sThumbName = $this->getThumbName($oFile->getBasename(), $iThumbSize);
                    $sThumbPath = $this->getThumbnailPath($sThumbName);

                    if (!file_exists($sThumbPath) || $blOverwrite) {
                        $this->createThumbnail($oFile->getBasename(), $iThumbSize, $blCrop);
                    }
                }
            }
        }
    }

    public function validateFilename($sFileName)
    {
        $aFileNameParts = explode('.', $sFileName);
        $aFileNameParts = array_reverse($aFileNameParts);
        $sFileNameExt = $aFileNameParts[0];
        foreach ($this->_aFileExtBlacklist as $sBlacklistPattern) {
            if (preg_match("/" . $sBlacklistPattern . "/", $sFileNameExt)) {
                throw new Exception(Registry::getLang()->translateString('DD_MEDIA_EXCEPTION_INVALID_FILEEXT'));
            }
        }
        return true;
    }
}
