<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

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