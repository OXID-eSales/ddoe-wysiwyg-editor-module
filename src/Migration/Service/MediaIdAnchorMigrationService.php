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
use OxidEsales\WysiwygModule\Migration\DTO\ContentMigrationResult;
use OxidEsales\WysiwygModule\Migration\DTO\ContentMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResult;
use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;

class MediaIdAnchorMigrationService implements MigrationServiceInterface
{
    /**
     * Attributes that may carry a media reference, in priority order
     */
    private const MEDIA_ATTRIBUTES = ['src', 'href'];

    private const OBSOLETE_ATTRIBUTES = ['data-filepath', 'data-filename'];

    private const NO_ENTRY_DETAIL = 'no matching entry in the media library';
    private const UNKNOWN_PATH_DETAIL = 'not recognized as a media library path';

    private const MEDIA_IMAGE_MARKER = 'dd-wysiwyg-media-image';
    private const MEDIA_DIRECTORY_MARKER = 'out/pictures/ddmedia/';
    private const MEDIA_URL_MARKER = 'oViewConf.getMediaUrl';

    private const CONVERTED_ANCHOR_PATTERN = '/{{\s*oeMediaUrl\(/i';

    public function __construct(
        private readonly MediaIdByPathFacadeInterface $mediaIdByPathFacade,
    ) {
    }

    public function migrateContent(string $content, string $key = ''): ContentMigrationResultInterface
    {
        /** @var MediaMigrationResultInterface[] $references */
        $references = [];

        $migratedContent = preg_replace_callback(
            '/(?<tag><(?:img|a)\b[^>]*>)/i',
            function (array $match) use ($key, &$references): string {
                return $this->modifyMediaTag($match['tag'], $key, $references);
            },
            $content
        );

        return new ContentMigrationResult((string)$migratedContent, $references);
    }

    /**
     * @param MediaMigrationResultInterface[] $references
     */
    private function modifyMediaTag(string $tag, string $key, array &$references): string
    {
        foreach (self::MEDIA_ATTRIBUTES as $attribute) {
            if (!preg_match($this->attributePattern($attribute), $tag, $matches)) {
                continue;
            }

            if ($this->isConvertibleMediaReference($tag, $matches['value'])) {
                return $this->replaceMediaReference($tag, $key, $attribute, $matches['value'], $references);
            }
        }

        return $tag;
    }

    /**
     * Matches the attribute where it starts one, i.e. after whitespace, so that attributes it is
     * only the ending of, e.g. data-src or lowsrc, are left alone
     */
    private function attributePattern(string $attribute): string
    {
        return '/(?<=\s)' . $attribute . '="(?<value>[^"]+)"/i';
    }

    /**
     * @param MediaMigrationResultInterface[] $references
     */
    private function replaceMediaReference(
        string $tag,
        string $key,
        string $attribute,
        string $path,
        array &$references
    ): string {
        try {
            $mediaId = $this->mediaIdByPathFacade->getMediaIdByPath($path);
        } catch (MediaNotFoundByFileInformationException) {
            $references[] = $this->failedReference($key, $attribute, $path, self::NO_ENTRY_DETAIL);

            return $tag;
        } catch (UnknownPathFormatException) {
            $references[] = $this->failedReference($key, $attribute, $path, self::UNKNOWN_PATH_DETAIL);

            return $tag;
        }

        $references[] = new MediaMigrationResult(
            key: $key,
            attribute: $attribute,
            path: $path,
            outcome: MigrationOutcome::Converted,
            mediaId: $mediaId,
        );

        return $this->writeMediaId($tag, $attribute, $mediaId);
    }

    private function failedReference(
        string $key,
        string $attribute,
        string $path,
        string $detail
    ): MediaMigrationResultInterface {
        return new MediaMigrationResult(
            key: $key,
            attribute: $attribute,
            path: $path,
            outcome: MigrationOutcome::Failed,
            detail: $detail,
        );
    }

    private function writeMediaId(string $tag, string $attribute, string $mediaId): string
    {
        $tag = (string)preg_replace(
            $this->attributePattern($attribute),
            $attribute . '="{{oeMediaUrl(\'' . $mediaId . '\')}}" data-id="' . $mediaId . '"',
            $tag,
            1
        );

        foreach (self::OBSOLETE_ATTRIBUTES as $obsoleteAttribute) {
            $tag = (string)preg_replace_callback(
                '/(?<boundary>[<"])[^<"]+' . $obsoleteAttribute . '="[^"]+"/i',
                static fn(array $match): string => $match['boundary'],
                $tag
            );
        }

        return $tag;
    }

    /**
     * Only images explicitly marked as media (dd-wysiwyg-media-image) or references pointing at a
     * media-library path are converted, and only as long as they do not already carry a media id
     * anchor. This leaves unrelated images and links (product images, external URLs, ordinary page
     * links, decorative theme assets) and already migrated content untouched.
     */
    private function isConvertibleMediaReference(string $tag, string $value): bool
    {
        if ($this->isAlreadyConvertedAnchor($value)) {
            return false;
        }

        return str_contains($tag, self::MEDIA_IMAGE_MARKER)
            || str_contains($value, self::MEDIA_DIRECTORY_MARKER)
            || str_contains($value, self::MEDIA_URL_MARKER);
    }

    private function isAlreadyConvertedAnchor(string $value): bool
    {
        return preg_match(self::CONVERTED_ANCHOR_PATTERN, $value) === 1;
    }
}
