<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Service;

use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProviderInterface;
use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;
use OxidEsales\WysiwygModule\Service\ModuleSettings;

class Media
{
    public const MEDIA_PATH = '/out/pictures/ddmedia/';
    public const MEDIA_PATH_SHORT = '/ddmedia/';
    public const AMOUNT_OF_FILES = "18";

    protected Connection $connection;

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
        'phar',
    ]; // regex allowed

    protected $_sFolderName;
    protected $_sFolderId;

    public function __construct(
        protected ModuleSettings $moduleSettings,
        protected Config $shopConfig,
        ConnectionProviderInterface $connectionProvider,
        protected UtilsObject $utilsObject
    ) {
        $this->connection = $connectionProvider->get();
    }

    public function setFolder($sFolderId = '')
    {
        $this->_sFolderId = $sFolderId;
        if ($sFolderId) {
            $this->setFolderNameForFolderId($sFolderId);
        }
    }

    public function getMediaPath($filename = ''): string
    {
        $this->_checkAndSetFolderName($filename);

        $sPath = $this->getPathToMediaFiles() . '/' . ($this->_sFolderName ? $this->_sFolderName . '/' : '');

        if ($filename) {
            return $sPath . (strpos($filename, 'thumbs/') !== false ? $filename : basename($filename));
        }

        return $sPath;
    }

    /**
     * todo: exception in place of bool response
     */
    public function getMediaUrl($filename = '')
    {
        $filepath = $this->getMediaPath($filename);

        if ($this->isAlternativeImageUrlConfigured()) {
            return $filepath;
        }

        if (!is_readable($filepath)) {
            return false;
        }

        if (strpos($filename, 'thumbs/') === false) {
            $filename = basename($filename);
        }

        return Path::join(
            $this->shopConfig->getSslShopUrl(),
            self::MEDIA_PATH,
            ($this->_sFolderName ? $this->_sFolderName . '/' : ''),
            $filename
        );
    }

    public function getThumbnailPath($filename = ''): string
    {
        return Path::join($this->getMediaPath(), 'thumbs', $filename);
    }

    public function getThumbnailUrl($sFile = '', $iThumbSize = null)
    {
        if ($sFile) {
            if (!$iThumbSize) {
                $iThumbSize = $this->getDefaultThumbnailSize();
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


    public function getThumbName($sFile, $iThumbSize = null)
    {
        if (!$iThumbSize) {
            $iThumbSize = $this->getDefaultThumbnailSize();
        }

        return str_replace('.', '_', md5(basename($sFile))) . '_thumb_' . $iThumbSize . '.jpg';
    }

    public function uploadeMedia($sSourcePath, $sDestPath, $sFileSize, $sFileType, $blCreateThumbs = false)
    {
        $this->createDirs();

        $sThumbName = '';

        $sDestPath = $this->_checkAndGetFileName($sDestPath);

        $sFileName = basename($sDestPath);
        $iFileCount = 0;

        $aResult = [];

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
                } catch (\Exception $e) {
                    $sThumbName = '';
                }
            }

            $aFile = [
                'filename' => $sFileName,
                'thumbnail' => $sThumbName,
            ];

            $sId = $this->generateUId();
            $sThumbName = $aFile['thumbnail'];
            $sFileName = $aFile['filename'];

            $sImageSize = '';
            if (is_readable($sDestPath) && preg_match("/image\//", $sFileType)) {
                $aImageSize = getimagesize($sDestPath);
                $sImageSize = ($aImageSize ? $aImageSize[0] . 'x' . $aImageSize[1] : '');
            }

            $iShopId = $this->shopConfig->getActiveShop()->getShopId();

            $sInsert = "REPLACE INTO `ddmedia`
                              ( `OXID`, 
                               `OXSHOPID`, 
                               `DDFILENAME`, 
                               `DDFILESIZE`, 
                               `DDFILETYPE`, 
                               `DDTHUMB`, 
                               `DDIMAGESIZE`, 
                               `DDFOLDERID` )
                            VALUES
                              ( ?, ?, ?, ?, ?, ?, ?, ? );";
            $this->connection->executeQuery($sInsert, [
                $sId,
                $iShopId,
                $sFileName,
                $sFileSize,
                $sFileType,
                $sThumbName,
                $sImageSize,
                $this->_sFolderId,
            ]);

            $aResult['id'] = $sId;
            $aResult['filename'] = $sFileName;
            $aResult['thumb'] = $this->getThumbnailUrl($sFileName);
            $aResult['imagesize'] = $sImageSize;
        }

        return $aResult;
    }


    public function validateFilename($sFileName)
    {
        $aFileNameParts = explode('.', $sFileName);
        $aFileNameParts = array_reverse($aFileNameParts);

        $sFileNameExt = $aFileNameParts[0];

        foreach ($this->_aFileExtBlacklist as $sBlacklistPattern) {
            if (preg_match("/" . $sBlacklistPattern . "/", $sFileNameExt)) {
                throw new \Exception(Registry::getLang()->translateString('DD_MEDIA_EXCEPTION_INVALID_FILEEXT'));
            }
        }

        return true;
    }


    public function createThumbnail($sFileName, $iThumbSize = null, $blCrop = true)
    {
        $sFilePath = $this->getMediaPath($sFileName);

        if (is_readable($sFilePath)) {
            if (!$iThumbSize) {
                $iThumbSize = $this->getDefaultThumbnailSize();
            }

            [$iImageWidth, $iImageHeight, $iImageType] = getimagesize($sFilePath);

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
                    throw new \Exception('Invalid filetype');
            }

            $iThumbWidth = $iImageWidth;
            $iThumbHeight = $iImageHeight;

            $iThumbX = 0;
            $iThumbY = 0;

            if ($blCrop) {
                if ($iImageWidth < $iImageHeight) {
                    $iThumbWidth = $iThumbSize;
                    $iThumbHeight = $iImageHeight / ($iImageWidth / $iThumbWidth);

                    $iThumbY = (int)(($iThumbSize - $iThumbHeight) / 2);
                } elseif ($iImageHeight < $iImageWidth) {
                    $iThumbHeight = $iThumbSize;
                    $iThumbWidth = $iImageWidth / ($iImageHeight / $iThumbHeight);

                    $iThumbX = (int)(($iThumbSize - $iThumbWidth) / 2);
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
            $iThumbWidth = (int)$iThumbWidth;
            $iThumbHeight = (int)$iThumbHeight;

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


    public function createMoreThumbnails($sFileName)
    {
        // More Thumbnail Sizes
        $this->createThumbnail($sFileName, 300);
        $this->createThumbnail($sFileName, 800);
    }


    public function createDirs()
    {
        if (!is_dir($this->getMediaPath())) {
            mkdir($this->getMediaPath());
        }

        if (!is_dir($this->getThumbnailPath())) {
            mkdir($this->getThumbnailPath());
        }
    }

    public function createCustomDir($sName)
    {
        $this->createDirs();

        $sPath = $this->getMediaPath();
        $sNewPath = $sPath . $sName;

        $sNewPath = $this->_checkAndGetFolderName($sNewPath, $sPath);

        if (!is_dir($sNewPath)) {
            mkdir($sNewPath);
        }

        $sFolderName = basename($sNewPath);

        $sId = $this->generateUId();

        $iShopId = $this->shopConfig->getActiveShop()->getShopId();

        $sInsert = "REPLACE INTO `ddmedia`
                          ( `OXID`, `OXSHOPID`, `DDFILENAME`, `DDFILESIZE`, `DDFILETYPE`, `DDTHUMB`, `DDIMAGESIZE` )
                        VALUES
                          ( ?, ?, ?, ?, ?, ?, ? );";

        $this->connection->executeQuery(
            $sInsert,
            [
                $sId,
                $iShopId,
                $sFolderName,
                0,
                'directory',
                '',
                '',
            ]
        );

        return ['id' => $sId, 'dir' => $sFolderName];
    }

    public function rename($sOldName, $sNewName, $sId, $sType = 'file')
    {
        $blReturn = false;

        // sanitize filename
        $sNewName = $this->_sanitizeFilename($sNewName);

        $sPath = $this->getMediaPath();

        $sOldPath = $sPath . $sOldName;
        $sNewPath = $sPath . $sNewName;

        if ($sType == 'directory') {
            $sNewPath = $this->_checkAndGetFolderName($sNewPath, $sPath);
        } else {
            $sNewPath = $this->_checkAndGetFileName($sNewPath);
        }

        if (rename($sOldPath, $sNewPath)) {
            $sNewName = basename($sNewPath);
            $iShopId = $this->shopConfig->getActiveShop()->getShopId();

            $sUpdate = "UPDATE `ddmedia`
                              SET `DDFILENAME` = '$sNewName' 
                            WHERE `OXID` = ? AND `OXSHOPID` = ?;";

            $this->connection->executeQuery(
                $sUpdate,
                [
                    $sId,
                    $iShopId,
                ]
            );

            $blReturn = true;
        }

        return $blReturn;
    }

    public function moveFileToFolder($sSourceFileID, $sTargetFolderID)
    {
        $blReturn = false;

        if ($sTargetFolderID) {
            $sSelect = "SELECT DDFILENAME FROM ddmedia WHERE OXID = ?";
            $sTargetFolderName = $this->connection->fetchOne($sSelect, [$sTargetFolderID]);

            $sSourceFileName = $sThumb = '';
            $sSelect = "SELECT DDFILENAME, DDTHUMB FROM ddmedia WHERE OXID = ?";
            $aData = $this->connection->fetchAllAssociative($sSelect, [$sSourceFileID]);
            if (count($aData)) {
                $sSourceFileName = $aData[0]['DDFILENAME'];
                $sThumb = $aData[0]['DDTHUMB'];
            }

            if ($sTargetFolderName && $sSourceFileName) {
                $sOldName = $this->getMediaPath() . $sSourceFileName;
                $sNewName = $this->getMediaPath() . $sTargetFolderName . '/' . $sSourceFileName;

                if (rename($sOldName, $sNewName)) {
                    if ($sThumb) {
                        $sOldThumbPath = $this->getMediaPath() . 'thumbs/';
                        $sNewThumbPath = $this->getMediaPath() . $sTargetFolderName . '/thumbs/';

                        if (!is_dir($sNewThumbPath)) {
                            mkdir($sNewThumbPath);
                        }

                        foreach (
                            Glob::glob(
                                $sOldThumbPath . str_replace(
                                    'thumb_' . $this->getDefaultThumbnailSize() . '.jpg',
                                    '*',
                                    $sThumb
                                )
                            ) as $sThumbFile
                        ) {
                            rename($sThumbFile, $sNewThumbPath . basename($sThumbFile));
                        }
                    }

                    $iShopId = $this->shopConfig->getActiveShop()->getShopId();

                    $sUpdate = "UPDATE `ddmedia`
                                      SET `DDFOLDERID` = ?  
                                    WHERE `OXID` = ? AND `OXSHOPID` = ?;";

                    $this->connection->executeQuery($sUpdate, [
                        $sTargetFolderID,
                        $sSourceFileID,
                        $iShopId,
                    ]);

                    $blReturn = true;
                }
            }
        }

        return $blReturn;
    }


    public function generateThumbnails($iThumbSize = null, $blOverwrite = false, $blCrop = true)
    {
        if (!$iThumbSize) {
            $iThumbSize = $this->getDefaultThumbnailSize();
        }

        if (is_dir($this->getMediaPath())) {
            foreach (new \DirectoryIterator($this->getMediaPath()) as $oFile) {
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

    /**
     * @param $sId
     *
     * @return void
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     */
    public function setFolderNameForFolderId($sId)
    {
        $iShopId = $this->shopConfig->getActiveShop()->getShopId();

        $sSelect = "SELECT `DDFILENAME` FROM `ddmedia` WHERE `OXID` = ? AND `DDFILETYPE` = ? AND `OXSHOPID` = ?";
        $folderName = $this->connection->fetchOne($sSelect, [$sId, 'directory', $iShopId]);
        $sFolderName = $folderName ?: '';

        if ($sFolderName) {
            $this->setFolderName($sFolderName);
        }
    }

    /**
     * @return mixed
     */
    public function getFolderName()
    {
        return $this->_sFolderName;
    }

    public function setFolderName($sFolderName)
    {
        $this->_sFolderName = $sFolderName;
    }

    /**
     * @param $sFile
     */
    protected function _checkAndSetFolderName($sFile)
    {
        if ($sFile && ($iPos = strpos($sFile, '/')) !== false) {
            $sFolderName = substr($sFile, 0, $iPos);
            if ($sFolderName != 'thumbs') {
                $this->_sFolderName = substr($sFile, 0, $iPos);
            }
        }
    }

    /**
     * @param $sNewPath
     * @param $sPath
     *
     * @return string
     */
    protected function _checkAndGetFolderName($sNewPath, $sPath)
    {
        while (file_exists($sNewPath)) {
            $sBaseName = basename($sNewPath);

            $aBaseParts = explode('_', $sBaseName);
            $aBaseParts = array_reverse($aBaseParts);

            $iFileCount = 0;
            if (strlen($aBaseParts[0]) && is_numeric($aBaseParts[0])) {
                $iFileCount = (int)$aBaseParts[0];
                unset($aBaseParts[0]);
            }

            $sBaseName = implode('_', array_reverse($aBaseParts));

            $sFileName = $sBaseName . '_' . (++$iFileCount);
            $sNewPath = $sPath . $sFileName;
        }

        return $sNewPath;
    }

    /**
     * @param $sDestPath
     *
     * @return array
     */
    protected function _checkAndGetFileName($sDestPath)
    {
        $iFileCount = 0;

        while (file_exists($sDestPath)) {
            $sFileName = basename($sDestPath);

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

        return $sDestPath;
    }

    /**
     * @param $sNewName
     *
     * @return mixed|null|string|string[]
     */
    protected function _sanitizeFilename($sNewName)
    {
        $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getEditLanguage();
        if ($aReplaceChars = \OxidEsales\Eshop\Core\Registry::getLang()->getSeoReplaceChars($iLang)) {
            $sNewName = str_replace(array_keys($aReplaceChars), array_values($aReplaceChars), $sNewName);
        }
        if (pathinfo($sNewName, PATHINFO_EXTENSION)) {
            $sNewName = preg_replace('/[^a-zA-Z0-9-_]+/', '-', pathinfo($sNewName, PATHINFO_FILENAME)) .
                        '.' .
                        pathinfo($sNewName, PATHINFO_EXTENSION);
        }

        return $sNewName;
    }

    private function getPathToMediaFiles(): string
    {
        if ($this->isAlternativeImageUrlConfigured()) {
            $basePath = $this->getAlternativeImageUrl();
            $mediaPath = self::MEDIA_PATH_SHORT;
        } else {
            $basePath = $this->shopConfig->getConfigParam('sShopDir');
            $mediaPath = self::MEDIA_PATH;
        }

        return Path::join($basePath, $mediaPath);
    }

    private function isAlternativeImageUrlConfigured(): bool
    {
        return (bool)$this->getAlternativeImageUrl();
    }

    private function getAlternativeImageUrl(): string
    {
        return $this->moduleSettings->getAlternativeImageDirectory();
    }

    public function getFileCount($iShopId = null)
    {
        $sSelect = "SELECT COUNT(*) AS 'count' FROM `ddmedia` WHERE 1 " .
                   ($iShopId != null ? "AND `OXSHOPID` = " . $this->connection->quote($iShopId) . " " : "") .
                   "AND `DDFOLDERID` = ?";

        $fileCount = $this->connection->fetchOne($sSelect, [$this->_sFolderId ?: '']);

        return $fileCount ?: 0;
    }

    public function getFiles($iStart = 0, $iShopId = null)
    {
        /** Cast $iStart parameter to int in order to avoid SQL injection */
        $iStart = (int)$iStart;
        $sSelect = "SELECT * FROM `ddmedia` WHERE 1 " .
                   ($iShopId != null ? "AND `OXSHOPID` = " . $this->connection->quote($iShopId) . " " : "") .
                   "AND `DDFOLDERID` = ? " .
                   "ORDER BY `OXTIMESTAMP` DESC LIMIT " . $iStart . ", " . self::AMOUNT_OF_FILES . " ";

        return $this->connection->fetchAllAssociative($sSelect, [$this->_sFolderId ?: '']);
    }

    public function delete($aIds)
    {
        foreach ($aIds as $iKey => $sId) {
            $aIds[$iKey] = $this->connection->quote($sId);
        }
        $sIds = implode(",", $aIds);

        $sSelect = "SELECT `OXID`, `DDFILENAME`, `DDTHUMB`, `DDFILETYPE`, `DDFOLDERID` 
                FROM `ddmedia` WHERE `OXID` IN($sIds) OR `DDFOLDERID` IN($sIds) ORDER BY `DDFOLDERID` ASC;";
        $aData = $this->connection->fetchAllAssociative($sSelect);

        $aFolders = [];
        foreach ($aData as $sKey => $aRow) {
            if ($aRow['DDFILETYPE'] == 'directory') {
                $aFolders[$aRow['OXID']] = $aRow['DDFILENAME'];
                unset($aData[$sKey]);
            }
        }

        foreach ($aData as $aRow) {
            if ($aRow['DDFILETYPE'] != 'directory') {
                $sFolderName = '';
                if ($aRow['DDFOLDERID'] && isset($aFolders[$aRow['DDFOLDERID']])) {
                    $sFolderName = $aFolders[$aRow['DDFOLDERID']];
                }
                unlink(Path::join($this->getMediaPath(), $sFolderName, $aRow['DDFILENAME']));

                if ($aRow['DDTHUMB']) {
                    $thumbFilename = 'thumb_' . $this->getDefaultThumbnailSize() . '.jpg';
                    $thumbs = Glob::glob(
                        Path::join(
                            $this->getMediaPath(),
                            $sFolderName,
                            'thumbs',
                            str_replace($thumbFilename, '*', $aRow['DDTHUMB'])
                        )
                    );
                    foreach ($thumbs as $sThumb) {
                        unlink($sThumb);
                    }
                }

                $sDelete = "DELETE FROM `ddmedia` WHERE `OXID` = '" . $aRow['OXID'] . "'; ";
                $this->connection->executeQuery($sDelete);
            }
        }

        // remove folder
        foreach ($aFolders as $sOxid => $sFolderName) {
            @rmdir(Path::join($this->getMediaPath(), $sFolderName, 'thumbs'));
            @rmdir(Path::join($this->getMediaPath(), $sFolderName));
            $sDelete = "DELETE FROM `ddmedia` WHERE `OXID` = '" . $sOxid . "'; ";
            $this->connection->executeQuery($sDelete);
        }
    }

    /**
     * @return string
     */
    protected function generateUId(): string
    {
        return $this->utilsObject->generateUId();
    }

    /**
     * @return int
     */
    public function getDefaultThumbnailSize(): int
    {
        return $this->_iDefaultThumbnailSize;
    }
}
