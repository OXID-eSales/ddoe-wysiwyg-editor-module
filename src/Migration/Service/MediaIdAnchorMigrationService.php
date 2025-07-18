<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\MediaLibrary\Compatibility\Factory\MediaFileInformationFactoryInterface;
use OxidEsales\MediaLibrary\Compatibility\Repository\PathMappingRepositoryInterface;

class MediaIdAnchorMigrationService implements MediaIdAnchorMigrationServiceInterface
{
    public function __construct(
        private readonly MediaFileInformationFactoryInterface $mediaFileInformationFactory,
        private readonly PathMappingRepositoryInterface $pathMappingRepository,
    ) {
    }

    public function migrateToMediaIdAnchors(string $content): string
    {
        $content = preg_replace_callback(
            '/<[^>]+dd-wysiwyg-media-image[^>]+>/msi',
            [$this, 'modifyMediaTag'],
            $content
        );

        return $content;
    }

    private function modifyMediaTag(array $oneMediaItem): string
    {
        $oneMediaItem = reset($oneMediaItem);

        if (preg_match('/src="(?<src>[^"]+)"/mi', $oneMediaItem, $matches)) {
            try {
                $fileInformation = $this->mediaFileInformationFactory->fromPath($matches['src']);
                $mediaId = $this->pathMappingRepository->getMediaIdByInformation($fileInformation);

                $oneMediaItem = preg_replace(
                    '/src="[^"]+"/mi',
                    'src="{{oeMediaUrl(\'' . $mediaId . '\')}}" data-id="' . $mediaId . '"',
                    $oneMediaItem
                );

                $cleanup = ['data-filepath', 'data-filename'];
                foreach ($cleanup as $key) {
                    $oneMediaItem = preg_replace('/([<"])[^<"]+' . $key . '="[^"]+"/mi', '$1', $oneMediaItem);
                }
            } catch (\Exception) {
            }
        }

        return $oneMediaItem;
    }
}
