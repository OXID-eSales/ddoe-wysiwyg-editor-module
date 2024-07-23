<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\HtmlFilter\HtmlFilterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

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
        $config = [
            'iEditorWidth' => $this->prepareSize($width),
            'iEditorHeight' => $this->prepareSize($height),
            'sEditorField' => $fieldName,
            'sEditorValue' => $this->filterContent($objectValue),
            'langabbr' => $this->settingsService->getInterfaceLanguageAbbreviation(),
            'blTextEditorDisabled' => $isEditorDisabled,
            'oViewConf' => $this->settingsService->getActiveViewConfig(),
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
