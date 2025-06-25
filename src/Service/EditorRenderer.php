<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\WysiwygModule\HtmlFilter\HtmlFilterInterface;

class EditorRenderer implements EditorRendererInterface
{
    public function __construct(
        protected TemplateRendererInterface $templateRenderer,
        protected SettingsInterface $settingsService,
        protected HtmlFilterInterface $htmlFilter
    ) {
    }

    public function render(
        string $width,
        string $height,
        string $objectValue,
        string $fieldName,
        bool $isEditorDisabled = false,
    ): string {
        // todo: prepare images
        $urls = [
            '055254e0af61e897dface96873f29254' => 'http://localhost.local/out/pictures/ddmedia/1028-536x354.jpg',
            'a7a8388ee560d72e2858681cc388a0e5' => 'http://localhost.local/out/pictures/ddmedia/1028-536x354.jpg',
            'abe2cefab95e8660cc5d5a53d2e09b97' => 'http://localhost.local/out/pictures/ddmedia/1028-536x354.jpg',
        ];

        $config = [
            'iEditorWidth' => $this->prepareSize($width),
            'iEditorHeight' => $this->prepareSize($height),
            'sEditorField' => $fieldName,
            'sEditorValue' => $this->filterContent($objectValue),
            'langabbr' => $this->settingsService->getInterfaceLanguageAbbreviation(),
            'blTextEditorDisabled' => $isEditorDisabled,
            'oViewConf' => $this->settingsService->getActiveViewConfig(),
            'contentMediaUrls' => $urls,
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

    private function filterContent(string $content): string
    {
        return $this->htmlFilter->filter($content);
    }
}
