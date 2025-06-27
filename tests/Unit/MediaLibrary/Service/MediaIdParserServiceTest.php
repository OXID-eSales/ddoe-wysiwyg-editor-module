<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace MediaLibrary\Service;

use OxidEsales\WysiwygModule\MediaLibrary\Service\MediaIdParserService;
use OxidEsales\WysiwygModule\MediaLibrary\Service\MediaIdParserServiceInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MediaIdParserServiceTest extends TestCase
{
    public static function contentsDataProvider(): \Generator
    {
        yield 'empty content' => [
            'content' => '',
            'expectedIds' => [],
        ];

        $randomId = uniqid();
        yield 'one placeholder' => [
            'content' => uniqid() . "{{oViewConf.getMediaUrl('" . $randomId . "')}}" . uniqid(),
            'expectedIds' => [$randomId],
        ];

        $randomId = uniqid() . '.' . uniqid();
        yield 'one placeholder with dot in id' => [
            'content' => uniqid() . "{{oViewConf.getMediaUrl('" . $randomId . "')}}" . uniqid(),
            'expectedIds' => [$randomId],
        ];

        $randomId = uniqid() . '_' . uniqid();
        yield 'one placeholder with underscore in id' => [
            'content' => uniqid() . "{{oViewConf.getMediaUrl('" . $randomId . "')}}" . uniqid(),
            'expectedIds' => [$randomId],
        ];

        $randomId1 = uniqid();
        $randomId2 = uniqid();
        yield 'multiple different placeholders' => [
            'content' => uniqid() . "{{oViewConf.getMediaUrl('" . $randomId1 . "')}}" . uniqid()
                . uniqid() . "{{oViewConf.getMediaUrl('" . $randomId2 . "')}}" . uniqid(),
            'expectedIds' => [$randomId1, $randomId2],
        ];

        $randomId1 = uniqid();
        $randomId2 = uniqid();
        yield 'multiple different placeholders with duplication' => [
            'content' => uniqid() . "{{oViewConf.getMediaUrl('" . $randomId1 . "')}}" . uniqid()
                . uniqid() . "{{oViewConf.getMediaUrl('" . $randomId2 . "')}}" . uniqid()
                . uniqid() . "{{oViewConf.getMediaUrl('" . $randomId1 . "')}}" . uniqid(),
            'expectedIds' => [$randomId1, $randomId2],
        ];
    }

    #[Test]
    #[DataProvider('contentsDataProvider')]
    public function parsesIdsFromContent(string $content, array $expectedIds): void
    {
        $sut = $this->getSut();
        $result = $sut->parseMediaIdsFromContent($content);

        $this->assertSame($expectedIds, $result);
    }

    public function getSut(): MediaIdParserServiceInterface
    {
        return new MediaIdParserService();
    }
}
