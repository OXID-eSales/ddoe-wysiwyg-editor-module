<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\DTO;

use OxidEsales\WysiwygModule\Migration\DTO\CustomAltTextTag;
use OxidEsales\WysiwygModule\Migration\DTO\CustomAltTextTagInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CustomAltTextTagTest extends TestCase
{
    #[Test]
    public function customAltTextTagExposesGivenValues(): void
    {
        $sut = $this->getSut(
            key: $key = uniqid('key'),
            tag: $tag = uniqid('tag'),
            mediaId: $mediaId = uniqid('mediaId'),
            altText: $altText = uniqid('altText'),
        );

        $this->assertSame($key, $sut->getKey());
        $this->assertSame($tag, $sut->getTag());
        $this->assertSame($mediaId, $sut->getMediaId());
        $this->assertSame($altText, $sut->getAltText());
    }

    private function getSut(string $key, string $tag, string $mediaId, string $altText): CustomAltTextTagInterface
    {
        return new CustomAltTextTag(
            key: $key,
            tag: $tag,
            mediaId: $mediaId,
            altText: $altText,
        );
    }
}
