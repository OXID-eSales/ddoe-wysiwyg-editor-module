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

        $this->assertEquals($html, $filter->filter($html));
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
    #[DataProvider('twigExpressionProvider')]
    public function filterKeepsTwigExpressionsInAttributesUnencoded(string $html): void
    {
        $removerSpy = $this->createMock(HtmlRemoverInterface::class);
        $filter = new HtmlFilter($removerSpy);

        $this->assertEquals($html, $filter->filter($html));
    }

    public static function twigExpressionProvider(): array
    {
        return [
            ['html' => '<a href="{{ seo_url({type: \'oxcontent\', ident: \'oxnewstlerinfo\'}) }}">news</a>'],
            ['html' => '<img src="{{oeMediaUrl(\'abc\')}}" data-id="abc" class="dd-wysiwyg-media-image">'],
            ['html' => '<a href="{{ seo_url({ident: \'oxnewstlerinfo\'}) }}">legacy</a>'],
        ];
    }

    #[Test]
    #[DataProvider('attributeBreakoutProvider')]
    public function filterDoesNotDecodeEncodedCharactersIntoAttributeBreakout(
        string $html,
        string $mustNotContain
    ): void {
        $removerSpy = $this->createMock(HtmlRemoverInterface::class);
        $filter = new HtmlFilter($removerSpy);

        $this->assertStringNotContainsString($mustNotContain, $filter->filter($html));
    }

    public static function attributeBreakoutProvider(): array
    {
        return [
            'encoded quote must not become a raw attribute break' => [
                'html' => '<a href="{{ seo_url({ident: \'&quot; onmouseover=alert(1) x\'}) }}">x</a>',
                'mustNotContain' => '" onmouseover=',
            ],
            'angle brackets must not become a tag' => [
                'html' => '<a href="{{ foo &lt;img src=x onerror=alert(1)&gt; }}">x</a>',
                'mustNotContain' => '<img',
            ],
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
