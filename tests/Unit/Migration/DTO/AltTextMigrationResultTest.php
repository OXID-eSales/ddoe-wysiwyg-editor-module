<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\DTO;

use OxidEsales\WysiwygModule\Migration\DTO\AltTextMigrationResult;
use OxidEsales\WysiwygModule\Migration\DTO\AltTextMigrationResultInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AltTextMigrationResultTest extends TestCase
{
    #[Test]
    public function implementsInterface(): void
    {
        $this->assertInstanceOf(AltTextMigrationResultInterface::class, $this->getSut());
    }

    #[Test]
    public function getContentReturnsConstructorValue(): void
    {
        $content = uniqid();

        $this->assertSame($content, $this->getSut(content: $content)->getContent());
    }

    #[Test]
    public function getCustomAltTextTagsDefaultsToEmptyArray(): void
    {
        $this->assertSame([], $this->getSut()->getCustomAltTextTags());
    }

    #[Test]
    public function getCustomAltTextTagsReturnsConstructorValue(): void
    {
        $tags = [
            [
                'tag' => uniqid(),
                'mediaId' => uniqid(),
                'altText' => uniqid(),
            ],
        ];

        $this->assertSame($tags, $this->getSut(customAltTextTags: $tags)->getCustomAltTextTags());
    }

    private function getSut(
        string $content = '',
        array $customAltTextTags = [],
    ): AltTextMigrationResult {
        return new AltTextMigrationResult(
            content: $content,
            customAltTextTags: $customAltTextTags,
        );
    }
}
