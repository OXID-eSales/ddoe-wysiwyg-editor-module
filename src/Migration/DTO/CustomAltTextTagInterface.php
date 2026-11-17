<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migration\DTO;

/**
 * A media image tag with a custom alt text, which the alt texts migration reports instead of changing.
 */
interface CustomAltTextTagInterface
{
    /**
     * Key of the row the tag was found in.
     */
    public function getKey(): string;

    public function getTag(): string;

    public function getMediaId(): string;

    public function getAltText(): string;
}
