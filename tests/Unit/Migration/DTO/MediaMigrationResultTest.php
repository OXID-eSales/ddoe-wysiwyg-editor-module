<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\DTO;

use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResult;
use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MediaMigrationResultTest extends TestCase
{
    #[Test]
    public function resultExposesWhatHappenedToTheReference(): void
    {
        $path = '/out/pictures/ddmedia/' . uniqid() . '.jpg';
        $mediaId = uniqid();

        $sut = new MediaMigrationResult(
            attribute: 'src',
            path: $path,
            outcome: MigrationOutcome::Converted,
            mediaId: $mediaId,
        );

        $this->assertSame('src', $sut->getAttribute());
        $this->assertSame($path, $sut->getPath());
        $this->assertSame(MigrationOutcome::Converted, $sut->getOutcome());
        $this->assertSame($mediaId, $sut->getMediaId());
        $this->assertSame('', $sut->getDetail());
        $this->assertSame('', $sut->getKey(), 'the row is unknown until the result is located');
    }

    #[Test]
    public function withKeyLocatesTheResultInItsRowWithoutTouchingTheOriginal(): void
    {
        $key = uniqid();
        $sut = $this->getSut();

        $located = $sut->withKey($key);

        $this->assertSame($key, $located->getKey());
        $this->assertSame('', $sut->getKey());
    }

    #[Test]
    public function withKeyKeepsEverythingElseAsItWas(): void
    {
        $sut = $this->getSut();

        $located = $sut->withKey(uniqid());

        $this->assertSame($sut->getAttribute(), $located->getAttribute());
        $this->assertSame($sut->getPath(), $located->getPath());
        $this->assertSame($sut->getOutcome(), $located->getOutcome());
        $this->assertSame($sut->getMediaId(), $located->getMediaId());
        $this->assertSame($sut->getDetail(), $located->getDetail());
    }

    private function getSut(): MediaMigrationResultInterface
    {
        return new MediaMigrationResult(
            attribute: 'href',
            path: '/out/pictures/ddmedia/' . uniqid() . '.jpg',
            outcome: MigrationOutcome::Failed,
            detail: 'no matching entry in the media library',
        );
    }
}
