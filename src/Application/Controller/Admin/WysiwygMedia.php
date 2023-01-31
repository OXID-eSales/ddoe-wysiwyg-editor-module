<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController;
use OxidEsales\Eshop\Application\Model\Content;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\WysiwygModule\Traits\ServiceContainer;
use Webmozart\PathUtil\Path;

/**
 * Class WysiwygMedia
 */
class WysiwygMedia extends AdminDetailsController
{
    use ServiceContainer;

    protected ?Media $mediaService = null;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = '@ddoewysiwyg/dialog/ddoewysiwygmedia';

    protected $_sUploadDir = '';
    protected $_sThumbDir = '';
    protected $_iDefaultThumbnailSize = 0;


    /**
     * Overrides oxAdminDetails::init()
     */
    public function init()
    {
        parent::init();

        if (Registry::getRequest()->getRequestEscapedParameter('folderid')) {
            $this->_sFolderId = Registry::getRequest()->getRequestEscapedParameter('folderid');
        }

        $this->mediaService = $this->getServiceFromContainer(Media::class);

        $this->mediaService->setFolder($this->_sFolderId);

        $this->_sUploadDir = $this->mediaService->getMediaPath();
        $this->_sThumbDir = $this->mediaService->getThumbnailPath();
        $this->_iDefaultThumbnailSize = $this->mediaService->getDefaultThumbnailSize();
    }

    /**
     * Overrides oxAdminDetails::render
     *
     * @return string
     */
    public function render()
    {
        $oConfig = Registry::getConfig();
        $iShopId = $oConfig->getActiveShop()->getShopId();

        $this->_aViewData['aFiles'] = $this->mediaService->getFiles(0, $iShopId);
        $this->_aViewData['iFileCount'] = $this->mediaService->getFileCount($iShopId);
        $this->_aViewData['sResourceUrl'] = $this->mediaService->getMediaUrl();
        $this->_aViewData['sThumbsUrl'] = $this->mediaService->getThumbnailUrl();
        $this->_aViewData['sFoldername'] = $this->mediaService->getFolderName();
        $this->_aViewData['sFolderId'] = $this->_sFolderId;
        $this->_aViewData['sTab'] = Registry::getRequest()->getRequestEscapedParameter('tab');

        $request = Registry::getRequest();
        $this->_aViewData["request"]["overlay"] = $request->getRequestParameter('overlay') ?: 0;

        return parent::render();
    }

    /**
     * Upload files
     */
    public function upload()
    {
        $request = Registry::getRequest();

        $sId = null;

        try {
            if ($_FILES) {
                $this->mediaService->createDirs();

                $sFileSize = $_FILES['file']['size'];
                $sFileType = $_FILES['file']['type'];

                $sSourcePath = $_FILES['file']['tmp_name'];
                $sDestPath = Path::join($this->mediaService->getMediaPath(), $_FILES['file']['name']);

                $this->mediaService->uploadeMedia($sSourcePath, $sDestPath, $sFileSize, $sFileType, true);
            }

            if ($request->getRequestParameter('src') == 'fallback') {
                $this->fallback(true);
            } else {
                header('Content-Type: application/json');
                die(
                json_encode([
                    'success'   => true,
                    'id'        => $sId,
                    'file'      => $sFileName ?? '',
                    'filepath'  => $sDestPath ?? '',
                    'filetype'  => $sFileType ?? '',
                    'filesize'  => $sFileSize ?? '',
                    'imagesize' => $sImageSize ?? '',
                ])
                );
            }
        } catch (\Exception $e) {
            if ($request->getRequestParameter('src') == 'fallback') {
                $this->fallback(false, true);
            } else {
                header('Content-Type: application/json');
                die(
                json_encode([
                    'success' => false,
                    'id'      => $sId,
                ])
                );
            }
        }
    }

    /**
     * todo: extract template
     *
     * @param bool $blComplete
     * @param bool $blError
     */
    public function fallback($blComplete = false, $blError = false)
    {
        $oViewConf = $this->getViewConfig();

        $sFormHTML = '<html><head></head><body style="text-align:center;">
          <form action="' . $oViewConf->getSelfLink()
            . 'cl=ddoewysiwygmedia_view&fnc=upload&src=fallback" method="post" enctype="multipart/form-data">
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
     * @return void
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function addFolder()
    {
        $oRequest = Registry::getRequest();

        if (($sName = $oRequest->getRequestEscapedParameter('name'))) {
            $aCustomDir = $this->mediaService->createCustomDir($sName);

            header('Content-Type: application/json');
            die(
            json_encode(
                [
                    'success'   => true,
                    'id'        => $aCustomDir['id'],
                    'file'      => $aCustomDir['dir'],
                    'filetype'  => 'directory',
                    'filesize'  => 0,
                    'imagesize' => '',
                ]
            )
            );
        } else {
            header('Content-Type: application/json');
            die(json_encode(['success' => false]));
        }
    }

    /**
     * @return void
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function rename()
    {
        $blReturn = false;
        $sMsg = '';

        $oRequest = Registry::getRequest();

        $sNewId = $sId = $oRequest->getRequestEscapedParameter('id');
        $sOldName = $oRequest->getRequestEscapedParameter('oldname');
        $sNewName = $oRequest->getRequestEscapedParameter('newname');
        $sFiletype = $oRequest->getRequestEscapedParameter('filetype');

        if ($sId && $sOldName && $sNewName) {
            //check if image is in use before moving it to another place
            $oContent = oxNew(Content::class);
            $blFileInUse = $oContent->checkIfMediaFileOrFolderIsInUse($this->mediaService->getMediaPath() . $sOldName);

            if (!$blFileInUse) {
                $blReturn = $this->mediaService->rename(
                    $sOldName,
                    $sNewName,
                    $sId,
                    $sFiletype
                );
            } else {
                $sMsg = 'DD_MEDIA_RENAME_FILE_ERR';
            }
        }

        header('Content-Type: application/json');
        die(json_encode(['success' => $blReturn, 'msg' => $sMsg, 'name' => $sNewName, 'id' => $sNewId]));
    }

    /**
     * Remove file
     */
    public function remove()
    {
        $blReturn = true;
        $sMsg = '';

        $request = Registry::getRequest();

        $sNames = $request->getRequestEscapedParameter('names');
        $aFilesInUse = [];
        if ($sNames && count($sNames)) {
            foreach ($sNames as $iKey => $sName)
            {
                //check if image is in use before moving it to another place
                $oContent = oxNew(Content::class);
                $blFileInUse = $oContent->checkIfMediaFileOrFolderIsInUse($this->mediaService->getMediaPath() . $sName);

                if (!$blFileInUse) {
                    if ($aIDs = $request->getRequestParameter('ids')) {
                        $this->mediaService->delete($aIDs);
                    }
                } else {
                    $aFilesInUse[] = $sName;
                }
            }
        }

        if( count($aFilesInUse))
        {
            $blReturn = false;

            // todo: output which files are in use and where
            $sMsg = 'DD_MEDIA_RENAME_FILE_ERR';
        }

        header('Content-Type: application/json');
        die(json_encode(['success' => $blReturn, 'msg' => $sMsg, 'in_use' => $aFilesInUse]));
    }

    public function movefile()
    {
        $blReturn = false;
        $sMsg = '';

        $oRequest = Registry::getRequest();

        $sSourceFileID = $oRequest->getRequestEscapedParameter('sourceid');
        $sFileName = $oRequest->getRequestEscapedParameter('file');
        $sTargetFolderID = $oRequest->getRequestEscapedParameter('targetid');
        $sTargetFolderName = $oRequest->getRequestEscapedParameter('folder');
        $sThumb = $oRequest->getRequestEscapedParameter('thumb');

        if ($sSourceFileID && $sFileName && $sTargetFolderID && $sTargetFolderName) {
            //check if image is in use before moving it to another place
            $oContent = oxNew(Content::class);
            $blFileInUse = $oContent->checkIfMediaFileOrFolderIsInUse($sFileName);

            if (!$blFileInUse && $this->mediaService->moveFileToFolder($sSourceFileID, $sTargetFolderID, $sThumb)) {
                $blReturn = true;
            } else {
                $sMsg = 'DD_MEDIA_MOVE_FILE_ERR';
            }
        }

        header('Content-Type: application/json');
        die(json_encode(['success' => $blReturn, 'msg' => $sMsg]));
    }

    /**
     * Load more files
     */
    public function moreFiles()
    {
        $oConfig = Registry::getConfig();
        $request = Registry::getRequest();

        $iStart = $request->getRequestParameter('start') ? $request->getRequestParameter('start') : 0;
        $iShopId = $oConfig->getActiveShop()->getShopId();

        $aFiles = $this->mediaService->getFiles($iStart, $iShopId);
        $blLoadMore = ($iStart + 18 < $this->mediaService->getFileCount($iShopId));

        header('Content-Type: application/json');
        die(json_encode(['files' => $aFiles, 'more' => $blLoadMore]));
    }

    /**
     * @return array
     */
    public function getBreadcrumb()
    {
        $aBreadcrumb = [];

        $oPath = new \stdClass();
        $oPath->active = ($this->mediaService->getFolderName() ? false : true);
        $oPath->name = 'Root';
        $aBreadcrumb[] = $oPath;

        if ($this->mediaService->getFolderName()) {
            $oPath = new \stdClass();
            $oPath->active = true;
            $oPath->name = $this->mediaService->getFolderName();
            $aBreadcrumb[] = $oPath;
        }

        return $aBreadcrumb;
    }
}