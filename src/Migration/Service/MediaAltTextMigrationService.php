<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\AltTextMigrationResult;

class MediaAltTextMigrationService implements AltTextMigrationServiceInterface
{
    private const MEDIA_IMAGE_TAG_PATTERN = '/<[^>]+dd-wysiwyg-media-image[^>]+>/msi';
    private const DATA_ID_PATTERN = '/data-id="(?<dataId>[^"]+)"/mi';
    private const ALT_ATTRIBUTE_PATTERN = '/alt="(?<alt>[^"]*)"/mi';
    private const ALT_ALREADY_MIGRATED_PATTERN = '/\{\{\s?oeMediaAlt\(/';

    /** @var array<int, array{tag: string, mediaId: string, altText: string}> */
    private array $customAltTextTags = [];

    public function migrateAltTexts(string $content): AltTextMigrationResult
    {
        $this->customAltTextTags = [];

        $modifiedContent = preg_replace_callback(
            self::MEDIA_IMAGE_TAG_PATTERN,
            [$this, 'processMediaTag'],
            $content
        );

        return new AltTextMigrationResult(
            content: $modifiedContent,
            customAltTextTags: $this->customAltTextTags,
        );
    }

    private function processMediaTag(array $matches): string
    {
        $tag = $matches[0];

        if (!preg_match(self::DATA_ID_PATTERN, $tag, $idMatches)) {
            return $tag;
        }

        $mediaId = $idMatches['dataId'];
        $altPlaceholder = "{{oeMediaAlt('" . $mediaId . "')}}";

        if (preg_match(self::ALT_ATTRIBUTE_PATTERN, $tag, $altMatches)) {
            $currentAlt = $altMatches['alt'];

            if (preg_match(self::ALT_ALREADY_MIGRATED_PATTERN, $currentAlt)) {
                return $tag;
            }

            if ($currentAlt !== '') {
                $this->customAltTextTags[] = [
                    'tag' => $tag,
                    'mediaId' => $mediaId,
                    'altText' => $currentAlt,
                ];
                return $tag;
            }

            return preg_replace(
                self::ALT_ATTRIBUTE_PATTERN,
                'alt="' . $altPlaceholder . '"',
                $tag
            );
        }

        return preg_replace(
            '/\s*>$/m',
            ' alt="' . $altPlaceholder . '">',
            $tag
        );
    }
}
