<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\MediaLibrary\Service;

class MediaIdParserService implements MediaIdParserServiceInterface
{
    /**
     * @todo-critical - regex covers oeMediaUrl only, but there are other media functions now.
     */
    public function parseMediaIdsFromContent(string $content): array
    {
        preg_match_all(
            "/{{\s?oeMediaUrl\([\"']([\w.]+)['\"]\)\s?}}/",
            $content,
            $matches
        );

        return array_unique($matches[1]);
    }
}
