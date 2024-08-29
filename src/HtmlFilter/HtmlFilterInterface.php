<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\HtmlFilter;

interface HtmlFilterInterface
{
    public function filter(string $html): string;
}
