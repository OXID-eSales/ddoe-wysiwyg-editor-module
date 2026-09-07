<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\DTO;

use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResult;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MediaMigrationResultTest extends TestCase
{
    #[Test]
    public function gettersReturnTheConstructorValues(): void
    {
        $key = uniqid('key-');
        $attribute = uniqid('attribute-');
        $path = uniqid('path-');
        $mediaId = uniqid('mediaId-');
        $detail = uniqid('detail-');

        $sut = new MediaMigrationResult(
            key: $key,
            attribute: $attribute,
            path: $path,
            outcome: MigrationOutcome::Failed,
            mediaId: $mediaId,
            detail: $detail,
        );

        $this->assertSame($key, $sut->getKey());
        $this->assertSame($attribute, $sut->getAttribute());
        $this->assertSame($path, $sut->getPath());
        $this->assertSame(MigrationOutcome::Failed, $sut->getOutcome());
        $this->assertSame($mediaId, $sut->getMediaId());
        $this->assertSame($detail, $sut->getDetail());
    }

    #[Test]
    public function mediaIdAndDetailAreEmptyByDefault(): void
    {
        $sut = new MediaMigrationResult(
            key: uniqid('key-'),
            attribute: uniqid('attribute-'),
            path: uniqid('path-'),
            outcome: MigrationOutcome::Converted,
        );

        $this->assertSame('', $sut->getMediaId());
        $this->assertSame('', $sut->getDetail());
    }
}
