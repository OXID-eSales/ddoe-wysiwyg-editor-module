<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\MediaLibrary;

use OxidEsales\MediaLibrary\Media\Repository\PreloadMediaRepositoryInterface;
use OxidEsales\MediaLibrary\Media\Service\MediaResourceInterface;

class MediaUrlsExtractorService implements MediaUrlsExtractorServiceInterface
{

    public function __construct(
        private readonly MediaIdParserServiceInterface $mediaIdParserService,
        private readonly PreloadMediaRepositoryInterface $preloadMediaRepository,
        private readonly MediaResourceInterface $mediaResource,
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
            $result[$oneId] = $this->mediaResource->getUrlToMedia($media);
        }

        return $result;
    }
}