<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\HtmlFilter;

use DOMDocument;
use DOMXPath;

class HtmlFilter implements HtmlFilterInterface
{
    public function __construct(private readonly HtmlRemoverInterface $htmlRemover)
    {
    }

    public function filter(string $html): string
    {
        $placeholders = [];
        $html = $this->protectTwigExpressions($html, $placeholders);

        $doc = $this->createDoc($html);
        $xpath = new DOMXPath($doc);
        foreach ($xpath->query('//script') as $node) {
            $this->htmlRemover->remove($node);
        }

        return strtr($this->getInnerHtml($doc), $placeholders);
    }

    private function protectTwigExpressions(string $html, array &$placeholders): string
    {
        $nonce = bin2hex(random_bytes(8));

        return preg_replace_callback(
            '/\{\{[^"<>]*?\}\}/',
            function (array $matches) use ($nonce, &$placeholders): string {
                $token = 'TWIGPLACEHOLDER' . $nonce . count($placeholders) . 'END';
                $placeholders[$token] = $matches[0];

                return $token;
            },
            $html
        );
    }

    private function createDoc(string $html): DOMDocument
    {
        // Ensure the correct encoding
        $contentType = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';

        $doc = new DOMDocument();
        $doc->loadHTML("$contentType<div>$html</div>", LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        return $doc;
    }

    private function getInnerHtml(DOMDocument $doc): string
    {
        $html = '';
        $div = $doc->documentElement->childNodes->item(0);
        foreach ($div->childNodes as $node) {
            $html .= $doc->saveHTML($node);
        }

        return $html;
    }
}
