<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\MediaLibrary\Service;

use OxidEsales\MediaLibrary\Media\Repository\PreloadMediaRepositoryInterface;
use OxidEsales\MediaLibrary\Media\Service\MediaObjectResourceInterface;

class MediaUrlsExtractorService implements MediaUrlsExtractorServiceInterface
{
    public function __construct(
        private readonly MediaIdParserServiceInterface $mediaIdParserService,
        private readonly PreloadMediaRepositoryInterface $preloadMediaRepository,
        private readonly MediaObjectResourceInterface $mediaObjectResource,
    ) {
    }

    public function getContentMediaUrls(string $content): array
    {
        $ids = $this->mediaIdParserService->parseMediaIdsFromContent($content);

        foreach ($ids as $oneId) {
            $this->preloadMediaRepository->registerForPreload($oneId);
        }

        $result = [];
        foreach ($ids as $oneId) {
            $media = $this->preloadMediaRepository->getMediaById($oneId);
            $result[$oneId] = $this->mediaObjectResource->getUrlToMedia($media);
        }

        return $result;
    }
}
