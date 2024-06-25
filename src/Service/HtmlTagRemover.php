<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Service;

use DOMNode;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\HtmlFilter\HtmlRemoverInterface;

class HtmlTagRemover implements HtmlRemoverInterface
{
    public function remove(DOMNode $node): void
    {
        $parent = $node->parentNode;
        while ($node->hasChildNodes()) {
            $parent->insertBefore($node->lastChild, $node->nextSibling);
        }
        $parent->removeChild($node);
    }
}
