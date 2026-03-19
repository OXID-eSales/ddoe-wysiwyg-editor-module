<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Service;

use OxidEsales\WysiwygModule\Migration\Service\MediaAltTextMigrationService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MediaAltTextMigrationServiceTest extends TestCase
{
    #[Test]
    public function noMediaImagesReturnsContentUnchanged(): void
    {
        $sut = new MediaAltTextMigrationService();

        $content = '<p>Some text</p><img src="regular.jpg" alt="regular">';
        $result = $sut->migrateAltTexts($content);

        $this->assertSame($content, $result->getContent());
        $this->assertSame([], $result->getCustomAltTextTags());
    }

    #[Test]
    public function emptyAltIsReplacedWithPlaceholder(): void
    {
        $sut = new MediaAltTextMigrationService();

        // phpcs:disable
        $content = '<img src="{{oeMediaUrl(\'abc123\')}}" data-id="abc123" data-source="media" class="dd-wysiwyg-media-image" alt="">';
        $expected = '<img src="{{oeMediaUrl(\'abc123\')}}" data-id="abc123" data-source="media" class="dd-wysiwyg-media-image" alt="{{oeMediaAlt(\'abc123\')}}">';
        // phpcs:enable

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($expected, $result->getContent());
        $this->assertSame([], $result->getCustomAltTextTags());
    }

    #[Test]
    public function missingAltAttributeGetsPlaceholderAdded(): void
    {
        $sut = new MediaAltTextMigrationService();

        // phpcs:disable
        $content = '<img src="{{oeMediaUrl(\'abc123\')}}" data-id="abc123" data-source="media" class="dd-wysiwyg-media-image">';
        $expected = '<img src="{{oeMediaUrl(\'abc123\')}}" data-id="abc123" data-source="media" class="dd-wysiwyg-media-image" alt="{{oeMediaAlt(\'abc123\')}}">';
        // phpcs:enable

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($expected, $result->getContent());
        $this->assertSame([], $result->getCustomAltTextTags());
    }

    #[Test]
    public function alreadyMigratedAltIsSkipped(): void
    {
        $sut = new MediaAltTextMigrationService();

        // phpcs:disable
        $content = '<img src="{{oeMediaUrl(\'abc123\')}}" data-id="abc123" data-source="media" class="dd-wysiwyg-media-image" alt="{{oeMediaAlt(\'abc123\')}}">';
        // phpcs:enable

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($content, $result->getContent());
        $this->assertSame([], $result->getCustomAltTextTags());
    }

    #[Test]
    public function customAltTextIsNotModifiedButReported(): void
    {
        $sut = new MediaAltTextMigrationService();

        // phpcs:disable
        $content = '<img src="{{oeMediaUrl(\'abc123\')}}" data-id="abc123" data-source="media" class="dd-wysiwyg-media-image" alt="My custom alt text">';
        // phpcs:enable

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($content, $result->getContent());
        $this->assertCount(1, $result->getCustomAltTextTags());
        $this->assertSame('abc123', $result->getCustomAltTextTags()[0]['mediaId']);
        $this->assertSame('My custom alt text', $result->getCustomAltTextTags()[0]['altText']);
    }

    #[Test]
    public function tagWithoutDataIdIsLeftUnchanged(): void
    {
        $sut = new MediaAltTextMigrationService();

        $content = '<img src="somefile.jpg" data-source="media" class="dd-wysiwyg-media-image" alt="">';

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($content, $result->getContent());
        $this->assertSame([], $result->getCustomAltTextTags());
    }

    #[Test]
    public function multipleImagesMixedCases(): void
    {
        $sut = new MediaAltTextMigrationService();

        // phpcs:disable
        $content = 'start '
            . '<img src="{{oeMediaUrl(\'id1\')}}" data-id="id1" class="dd-wysiwyg-media-image" alt="">'
            . ' middle '
            . '<img src="{{oeMediaUrl(\'id2\')}}" data-id="id2" class="dd-wysiwyg-media-image" alt="custom">'
            . ' end '
            . '<img src="{{oeMediaUrl(\'id3\')}}" data-id="id3" class="dd-wysiwyg-media-image" alt="{{oeMediaAlt(\'id3\')}}">';

        $expected = 'start '
            . '<img src="{{oeMediaUrl(\'id1\')}}" data-id="id1" class="dd-wysiwyg-media-image" alt="{{oeMediaAlt(\'id1\')}}">'
            . ' middle '
            . '<img src="{{oeMediaUrl(\'id2\')}}" data-id="id2" class="dd-wysiwyg-media-image" alt="custom">'
            . ' end '
            . '<img src="{{oeMediaUrl(\'id3\')}}" data-id="id3" class="dd-wysiwyg-media-image" alt="{{oeMediaAlt(\'id3\')}}">';
        // phpcs:enable

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($expected, $result->getContent());
        $this->assertCount(1, $result->getCustomAltTextTags());
        $this->assertSame('id2', $result->getCustomAltTextTags()[0]['mediaId']);
        $this->assertSame('custom', $result->getCustomAltTextTags()[0]['altText']);
    }

    #[Test]
    public function multilineTagIsHandledCorrectly(): void
    {
        $sut = new MediaAltTextMigrationService();

        // phpcs:disable
        $content = '<img src="{{oeMediaUrl(\'abc123\')}}"
            data-id="abc123"
            data-source="media"
            class="dd-wysiwyg-media-image"
            alt="">';
        $expected = '<img src="{{oeMediaUrl(\'abc123\')}}"
            data-id="abc123"
            data-source="media"
            class="dd-wysiwyg-media-image"
            alt="{{oeMediaAlt(\'abc123\')}}">';
        // phpcs:enable

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($expected, $result->getContent());
        $this->assertSame([], $result->getCustomAltTextTags());
    }

    #[Test]
    public function stateIsResetBetweenCalls(): void
    {
        $sut = new MediaAltTextMigrationService();

        // phpcs:disable
        $contentWithCustom = '<img src="{{oeMediaUrl(\'abc\')}}" data-id="abc" class="dd-wysiwyg-media-image" alt="custom">';
        // phpcs:enable

        $result1 = $sut->migrateAltTexts($contentWithCustom);
        $this->assertCount(1, $result1->getCustomAltTextTags());

        $result2 = $sut->migrateAltTexts('no media here');
        $this->assertSame([], $result2->getCustomAltTextTags());
    }
}
