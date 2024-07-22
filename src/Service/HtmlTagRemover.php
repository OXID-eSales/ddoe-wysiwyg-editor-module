<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Service;

use DOMNode;
use InvalidArgumentException;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\HtmlFilter\HtmlRemoverInterface;

class HtmlTagRemover implements HtmlRemoverInterface
{
    public function remove(DOMNode $node): void
    {
        if ($node->parentNode === null) {
            throw new InvalidArgumentException("The node does not have a parent.");
        }

        $parent = $node->parentNode;
        while ($node->hasChildNodes()) {
            $parent->insertBefore($node->lastChild, $node->nextSibling);
        }
        $parent->removeChild($node);
    }
}
