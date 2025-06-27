<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\MediaLibrary;

interface MediaIdParserServiceInterface
{

    public function parseMediaIdsFromContent(string $content): array;
}