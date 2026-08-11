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
use OxidEsales\WysiwygModule\Migration\DTO\MediaReferenceResult;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use Psr\Log\LoggerInterface;

class MediaIdAnchorMigrationService implements MigrationServiceInterface
{
    private const MEDIA_TAG_PATTERN = '/<(?:img|a)\b[^>]*>/msi';

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

    public function __construct(
        private readonly MediaIdByPathFacadeInterface $mediaIdByPathFacade,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function migrateContent(string $content): string
    {
        return $this->migrateContentWithReferences($content)->getContent();
    }

    public function migrateContentWithReferences(string $content): ContentMigrationResultInterface
    {
        /** @var MediaReferenceResult[] $references */
        $references = [];

        $migratedContent = preg_replace_callback(
            self::MEDIA_TAG_PATTERN,
            function (array $matchedTag) use (&$references): string {
                return $this->modifyMediaTag($matchedTag[0], $references);
            },
            $content
        );

        return new ContentMigrationResult((string)$migratedContent, $references);
    }

    /**
     * @param MediaReferenceResult[] $references
     */
    private function modifyMediaTag(string $tag, array &$references): string
    {
        foreach (self::MEDIA_ATTRIBUTES as $attribute) {
            if (!preg_match('/' . $attribute . '="(?<value>[^"]+)"/mi', $tag, $matches)) {
                continue;
            }

            if ($this->isConvertibleMediaReference($tag, $matches['value'])) {
                return $this->replaceMediaReference($tag, $attribute, $matches['value'], $references);
            }
        }

        return $tag;
    }

    /**
     * @param MediaReferenceResult[] $references
     */
    private function replaceMediaReference(string $tag, string $attribute, string $path, array &$references): string
    {
        try {
            $mediaId = $this->mediaIdByPathFacade->getMediaIdByPath($path);
        } catch (MediaNotFoundByFileInformationException $exception) {
            $references[] = $this->reportFailure($attribute, $path, self::NO_ENTRY_DETAIL, $exception);

            return $tag;
        } catch (UnknownPathFormatException $exception) {
            $references[] = $this->reportFailure($attribute, $path, self::UNKNOWN_PATH_DETAIL, $exception);

            return $tag;
        }

        $references[] = new MediaReferenceResult(
            attribute: $attribute,
            path: $path,
            outcome: MigrationOutcome::Converted,
            mediaId: $mediaId,
        );

        return $this->writeMediaId($tag, $attribute, $mediaId);
    }

    private function reportFailure(
        string $attribute,
        string $path,
        string $detail,
        \Exception $exception
    ): MediaReferenceResult {
        $this->logger->warning($exception->getMessage());

        return new MediaReferenceResult(
            attribute: $attribute,
            path: $path,
            outcome: MigrationOutcome::Failed,
            detail: $detail,
        );
    }

    private function writeMediaId(string $tag, string $attribute, string $mediaId): string
    {
        $tag = (string)preg_replace(
            '/' . $attribute . '="[^"]+"/mi',
            $attribute . '="{{oeMediaUrl(\'' . $mediaId . '\')}}" data-id="' . $mediaId . '"',
            $tag
        );

        foreach (self::OBSOLETE_ATTRIBUTES as $key) {
            $tag = (string)preg_replace('/([<"])[^<"]+' . $key . '="[^"]+"/mi', '$1', $tag);
        }

        return $tag;
    }

    /**
     * Only images explicitly marked as media (dd-wysiwyg-media-image) or references pointing at a
     * media-library path are converted. This leaves unrelated images and links (product images,
     * external URLs, ordinary page links, decorative theme assets) untouched.
     */
    private function isConvertibleMediaReference(string $tag, string $value): bool
    {
        return str_contains($tag, self::MEDIA_IMAGE_MARKER)
            || str_contains($value, self::MEDIA_DIRECTORY_MARKER)
            || str_contains($value, self::MEDIA_URL_MARKER);
    }
}
