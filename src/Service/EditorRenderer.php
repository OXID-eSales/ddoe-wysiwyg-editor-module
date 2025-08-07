<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\WysiwygModule\HtmlFilter\HtmlFilterInterface;
use OxidEsales\WysiwygModule\MediaLibrary\Service\MediaUrlsExtractorServiceInterface;

class EditorRenderer implements EditorRendererInterface
{
    public function __construct(
        protected TemplateRendererInterface $templateRenderer,
        protected SettingsInterface $settingsService,
        protected HtmlFilterInterface $htmlFilter,
        private readonly MediaUrlsExtractorServiceInterface $mediaUrlsExtractorService,
    ) {
    }

    // todo: extract template parameters calculation to a separate class
    // todo: decorate parameters calculator with html filter part
    // todo: decorate parameters calculator with the media urls part
    public function render(
        string $width,
        string $height,
        string $objectValue,
        string $fieldName,
        bool $isEditorDisabled = false,
    ): string {
        $config = [
            'iEditorWidth' => $this->prepareSize($width),
            'iEditorHeight' => $this->prepareSize($height),
            'sEditorField' => $fieldName,
            'sEditorValue' => $this->htmlFilter->filter($objectValue),
            'langabbr' => $this->settingsService->getInterfaceLanguageAbbreviation(),
            'blTextEditorDisabled' => $isEditorDisabled,
            'oViewConf' => $this->settingsService->getActiveViewConfig(),
            'contentMediaUrls' => $this->mediaUrlsExtractorService->getContentMediaUrls($objectValue),
        ];

        return $this->templateRenderer->renderTemplate('@ddoewysiwyg/ddoewysiwyg', $config);
    }

    private function prepareSize(string $sizeValue): string
    {
        if ($this->checkIfOnlyDigitsInValue($sizeValue)) {
            $sizeValue .= 'px';
        }

        return $sizeValue;
    }

    private function checkIfOnlyDigitsInValue(string $sizeValue): bool
    {
        return (bool)preg_match("/^\d+$/i", $sizeValue);
    }
}
