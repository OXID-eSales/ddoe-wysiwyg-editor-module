<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\MediaLibrary\Compatibility\Exception\MediaNotFoundByFileInformationException;
use OxidEsales\MediaLibrary\Compatibility\Exception\UnknownPathFormatException;
use OxidEsales\MediaLibrary\Compatibility\Facade\MediaIdByPathFacadeInterface;
use Psr\Log\LoggerInterface;

class MediaIdAnchorMigrationService implements MigrationServiceInterface
{
    public function __construct(
        private readonly MediaIdByPathFacadeInterface $mediaIdByPathFacade,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function migrateContent(string $content): string
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
                $mediaId = $this->mediaIdByPathFacade->getMediaIdByPath($matches['src']);

                $oneMediaItem = preg_replace(
                    '/src="[^"]+"/mi',
                    'src="{{oeMediaUrl(\'' . $mediaId . '\')}}" data-id="' . $mediaId . '"',
                    $oneMediaItem
                );

                $cleanup = ['data-filepath', 'data-filename'];
                foreach ($cleanup as $key) {
                    $oneMediaItem = preg_replace('/([<"])[^<"]+' . $key . '="[^"]+"/mi', '$1', $oneMediaItem);
                }
            } catch (MediaNotFoundByFileInformationException | UnknownPathFormatException $exception) {
                $this->logger->warning($exception->getMessage());
            }
        }

        return $oneMediaItem;
    }
}
