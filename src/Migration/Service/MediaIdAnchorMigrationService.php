<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\Service;

use OxidEsales\MediaLibrary\Compatibility\Exception\MediaNotFoundByFileInformationException;
use OxidEsales\MediaLibrary\Compatibility\Exception\UnknownPathFormatException;
use OxidEsales\MediaLibrary\Compatibility\Service\MediaByPathImportServiceInterface;
use Psr\Log\LoggerInterface;

class MediaIdAnchorMigrationService implements MigrationServiceInterface
{
    public function __construct(
        private readonly MediaByPathImportServiceInterface $mediaByPathImportService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Attributes that may carry a media reference, in priority order
     */
    private const MEDIA_ATTRIBUTES = ['src', 'href'];

    public function migrateContent(string $content): string
    {
        $content = preg_replace_callback(
            '/<(?:img|a)\b[^>]*>/msi',
            [$this, 'modifyMediaTag'],
            $content
        );

        return $content;
    }

    private function modifyMediaTag(array $matchedTag): string
    {
        $tag = reset($matchedTag);

        foreach (self::MEDIA_ATTRIBUTES as $attribute) {
            if (!preg_match('/' . $attribute . '="(?<value>[^"]+)"/mi', $tag, $matches)) {
                continue;
            }

            if ($this->isConvertibleMediaReference($tag, $matches['value'])) {
                return $this->replaceMediaReference($tag, $attribute, $matches['value']);
            }
        }

        return $tag;
    }

    private function replaceMediaReference(string $tag, string $attribute, string $value): string
    {
        try {
            $mediaId = $this->mediaByPathImportService->getOrCreateMediaIdByPath($value);

            $tag = preg_replace(
                '/' . $attribute . '="[^"]+"/mi',
                $attribute . '="{{oeMediaUrl(\'' . $mediaId . '\')}}" data-id="' . $mediaId . '"',
                $tag
            );

            $cleanup = ['data-filepath', 'data-filename'];
            foreach ($cleanup as $key) {
                $tag = preg_replace('/([<"])[^<"]+' . $key . '="[^"]+"/mi', '$1', $tag);
            }
        } catch (MediaNotFoundByFileInformationException | UnknownPathFormatException $exception) {
            $this->logger->warning($exception->getMessage());
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
        return str_contains($tag, 'dd-wysiwyg-media-image')
            || str_contains($value, 'out/pictures/ddmedia/')
            || str_contains($value, 'oViewConf.getMediaUrl');
    }
}
