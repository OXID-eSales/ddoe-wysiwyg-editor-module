<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\HtmlFilter;

use DOMNode;
use OxidEsales\WysiwygModule\HtmlFilter\HtmlFilter;
use OxidEsales\WysiwygModule\HtmlFilter\HtmlRemoverInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HtmlFilterTest extends TestCase
{
    #[Test]
    #[DataProvider('noScriptTagsProvider')]
    public function filterDoesNotRemoveWhenNoScriptTagsFound(string $html): void
    {
        $removerSpy = $this->createMock(HtmlRemoverInterface::class);
        $removerSpy
            ->expects($this->never())
            ->method('remove');
        $filter = new HtmlFilter($removerSpy);

        $filter->filter($html);
    }

    public static function noScriptTagsProvider(): array
    {
        return [
            ['html' => ''],
            ['html' => '<div></div>'],
            ['html' => '<div><span>content-šÄßüл</span><b>šÄßüл</b></div>'],
        ];
    }

    #[Test]
    #[DataProvider('oneScriptTagProvider')]
    public function filterRemovesOneScriptTag(string $html): void
    {
        $removerSpy = $this->createMock(HtmlRemoverInterface::class);
        $removerSpy
            ->expects($this->once())
            ->method('remove')
            ->with($this->callback(function (DOMNode $node) {
                return $node->nodeName == 'script' && $node->textContent == '//content-šÄßüл';
            }));
        $filter = new HtmlFilter($removerSpy);

        $filter->filter($html);
    }

    public static function oneScriptTagProvider(): array
    {
        return [
            ['html' => '<div><script>//content-šÄßüл</script></div>'],
            ['html' => '<div><span>content-šÄßüл</span><script>//content-šÄßüл</script></div>'],
            ['html' => '<div><span>content-šÄßüл<script>//content-šÄßüл</script></span><b>šÄßüл</b></div>'],
        ];
    }

    #[Test]
    public function filterRemovesOneClosedScriptTag(): void
    {
        $removerSpy = $this->createMock(HtmlRemoverInterface::class);
        $removerSpy
            ->expects($this->once())
            ->method('remove')
            ->with($this->callback(function (DOMNode $node) {
                $isAttributeValid = count($node->attributes) == 1
                    && $node->attributes[0]->nodeName == 'src'
                    && $node->attributes[0]->nodeValue == 'app.js';
                return $node->nodeName == 'script' && $isAttributeValid;
            }));
        $filter = new HtmlFilter($removerSpy);

        $filter->filter('<div><script src="app.js"/></div>');
    }

    #[Test]
    public function filterRemovesManyScriptTags(): void
    {
        $removerSpy = $this->createMock(HtmlRemoverInterface::class);
        $removerSpy
            ->expects($this->exactly(2))
            ->method('remove')
            ->with($this->callback(function (DOMNode $node) {
                return $node->nodeName == 'script' && $node->textContent == '//content-šÄßüл';
            }));
        $filter = new HtmlFilter($removerSpy);

        $filter->filter('<div><script>//content-šÄßüл</script><script>//content-šÄßüл</script></div>');
    }
}
