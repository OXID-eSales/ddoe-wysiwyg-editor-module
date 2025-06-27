<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\MediaLibrary\Service;

interface MediaUrlsExtractorServiceInterface
{
    public function getContentMediaUrls(string $content): array;
}