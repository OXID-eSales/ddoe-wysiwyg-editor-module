<?php

/**
 *
 *     |o     o    |          |
 * ,---|.,---..,---|,---.,---.|__/
 * |   |||   |||   ||---'`---.|  \
 * `---'``---|``---'`---'`---'`   `
 *       `---'    [media solutions]
 *
 * @copyright   (c) digidesk - media solutions
 * @link            https://www.digidesk.de
 */

namespace OxidEsales\WysiwygModule\Tests\Unit\Service;

class MediaMock extends \OxidEsales\WysiwygModule\Service\Media
{
    public function createThumbnail($sFileName, $iThumbSize = null, $blCrop = true)
    {
        $sFilePath = $this->getMediaPath($sFileName);

        if (is_readable($sFilePath)) {
            if (!$iThumbSize) {
                $iThumbSize = $this->getDefaultThumbnailSize();
            }

            $sThumbName = $this->getThumbName($sFileName, $iThumbSize);

            copy($sFilePath, $this->getThumbnailPath($sThumbName));

            return $sThumbName;
        }

        return false;
    }

    protected function moveUploadedFile($sSourcePath, array|string $sDestPath): bool
    {
        return rename($sSourcePath, $sDestPath);
    }

    protected function getImageSize(array|string $sDestPath): array|false
    {
        return [
            'width' => '300',
            'height' => '300',
            'type' => IMAGETYPE_JPEG,
            'attr' => 'height="300" width="300"'
        ];
    }
}