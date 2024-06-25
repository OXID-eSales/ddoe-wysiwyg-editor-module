<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Service;

use DOMDocument;
use OxidEsales\WysiwygModule\Service\HtmlTagRemover;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HtmlTagRemoverTest extends TestCase
{
    #[Test]
    #[DataProvider('htmlProvider')]
    public function remove(string $node, string $html, string $expectedHtml): void
    {
        $doc = new DOMDocument();
        $doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $node = $doc->getElementsByTagName($node)->item(0);

        $remover = new HtmlTagRemover();
        $remover->remove($node);

        $this->assertEquals($expectedHtml, rtrim($doc->saveHTML()));
    }

    public static function htmlProvider(): array
    {
        return [
            [
                'node' => 'span',
                'html' => '<div><span>content</span></div>',
                'expectedHtml' => '<div>content</div>',
            ],
            [
                'node' => 'script',
                'html' => '<div><script>//content</script></div>',
                'expectedHtml' => '<div>//content</div>',
            ],
            [
            'node' => 'script',
            'html' => '<div><script src="app.js"/></div>',
            'expectedHtml' => '<div></div>',
            ],
        ];
    }
}
