<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\MediaLibrary\Service;

class MediaIdParserService implements MediaIdParserServiceInterface
{
    public function parseMediaIdsFromContent(string $content): array
    {
        preg_match_all(
            "/{{oeMediaUrl\('([\w.]+)'\)}}/",
            $content,
            $matches
        );

        return array_unique($matches[1]);
    }
}
