<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\HtmlFilter;

interface HtmlFilterInterface
{
    public function filter(string $html): string;
}
