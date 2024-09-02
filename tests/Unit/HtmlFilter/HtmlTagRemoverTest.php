<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\HtmlFilter;

use DOMDocument;
use InvalidArgumentException;
use OxidEsales\WysiwygModule\HtmlFilter\HtmlTagRemover;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HtmlTagRemoverTest extends TestCase
{
    #[Test]
    public function throwExceptionOnNoParentNode(): void
    {
        $doc = new DOMDocument();
        $node = $doc->createElement('div');

        $remover = new HtmlTagRemover();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The node does not have a parent.");

        $remover->remove($node);
    }

    #[Test]
    #[DataProvider('htmlProvider')]
    public function removeNode(string $node, string $html, string $expectedHtml): void
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
