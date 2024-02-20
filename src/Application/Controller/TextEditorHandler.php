<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Application\Controller;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\WysiwygModule\Service\EditorRendererInterface;

/**
 * Class TextEditorHandler
 *
 * @mixin \OxidEsales\Eshop\Application\Controller\TextEditorHandler
 */
class TextEditorHandler extends TextEditorHandler_parent
{
    /**
     * Render text editor.
     *
     * @param int $width The editor width
     * @param int $height The editor height
     * @param object $objectValue The object value passed to editor
     * @param string $fieldName The name of object field which content is passed to editor
     *
     * @return string The Editor output
     */
    public function renderRichTextEditor($width, $height, $objectValue, $fieldName)
    {
        $editorRenderer = $this->getWysiwywEditorRenderer();

        return $editorRenderer->render(
            width: $width,
            height: $height,
            objectValue: (string)$objectValue,
            fieldName: $fieldName,
            isEditorDisabled: $this->isTextEditorDisabled()
        );
    }

    public function getWysiwywEditorRenderer(): EditorRendererInterface
    {
        return ContainerFacade::get(EditorRendererInterface::class);
    }
}
