<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\MediaLibrary\Service;

use OxidEsales\MediaLibrary\Media\Exception\MediaNotFoundException;
use OxidEsales\MediaLibrary\Media\Facade\MediaFacadeInterface;

class MediaUrlsExtractorService implements MediaUrlsExtractorServiceInterface
{
    public function __construct(
        private readonly MediaIdParserServiceInterface $mediaIdParserService,
        private readonly MediaFacadeInterface $mediaFacade,
    ) {
    }

    public function getContentMediaUrls(string $content): array
    {
        $ids = $this->mediaIdParserService->parseMediaIdsFromContent($content);
        $this->mediaFacade->registerForPreload(...$ids);

        $result = [];
        foreach ($ids as $oneId) {
            try {
                $mediaUrl = $this->mediaFacade->getMediaUrl($oneId);
            } catch (MediaNotFoundException) {
                $mediaUrl = '';
            }

            $result[$oneId] = $mediaUrl;
        }

        return $result;
    }
}
