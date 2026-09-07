<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Service;

interface EditorRendererInterface
{
    public function render(
        string $width,
        string $height,
        string $objectValue,
        string $fieldName,
        bool $isEditorDisabled = false,
    ): string;
}
