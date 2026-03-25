<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Service;

use OxidEsales\WysiwygModule\Migration\Service\MediaAltTextMigrationService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MediaAltTextMigrationServiceTest extends TestCase
{
    #[Test]
    public function noMediaImagesReturnsContentUnchanged(): void
    {
        $sut = $this->getSut();

        $src = uniqid();
        $alt = uniqid();
        $content = '<p>Some text</p><img src="' . $src . '" alt="' . $alt . '">';
        $result = $sut->migrateAltTexts($content);

        $this->assertSame($content, $result->getContent());
        $this->assertSame([], $result->getCustomAltTextTags());
    }

    #[Test]
    public function emptyAltIsReplacedWithPlaceholder(): void
    {
        $sut = $this->getSut();

        $mediaId = uniqid();
        $content = '<img'
            . " src=\"{{oeMediaUrl('$mediaId')}}\""
            . " data-id=\"$mediaId\""
            . ' data-source="media"'
            . ' class="dd-wysiwyg-media-image"'
            . ' alt="">';
        $expected = '<img'
            . " src=\"{{oeMediaUrl('$mediaId')}}\""
            . " data-id=\"$mediaId\""
            . ' data-source="media"'
            . ' class="dd-wysiwyg-media-image"'
            . " alt=\"{{oeMediaAlt('$mediaId')}}\">";

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($expected, $result->getContent());
        $this->assertSame([], $result->getCustomAltTextTags());
    }

    #[Test]
    public function missingAltAttributeGetsPlaceholderAdded(): void
    {
        $sut = $this->getSut();

        $mediaId = uniqid();
        $content = '<img'
            . " src=\"{{oeMediaUrl('$mediaId')}}\""
            . " data-id=\"$mediaId\""
            . ' data-source="media"'
            . ' class="dd-wysiwyg-media-image">';
        $expected = '<img'
            . " src=\"{{oeMediaUrl('$mediaId')}}\""
            . " data-id=\"$mediaId\""
            . ' data-source="media"'
            . ' class="dd-wysiwyg-media-image"'
            . " alt=\"{{oeMediaAlt('$mediaId')}}\">";

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($expected, $result->getContent());
        $this->assertSame([], $result->getCustomAltTextTags());
    }

    #[Test]
    public function alreadyMigratedAltIsSkipped(): void
    {
        $sut = $this->getSut();

        $mediaId = uniqid();
        $content = '<img'
            . " src=\"{{oeMediaUrl('$mediaId')}}\""
            . " data-id=\"$mediaId\""
            . ' data-source="media"'
            . ' class="dd-wysiwyg-media-image"'
            . " alt=\"{{oeMediaAlt('$mediaId')}}\">";

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($content, $result->getContent());
        $this->assertSame([], $result->getCustomAltTextTags());
    }

    #[Test]
    public function customAltTextIsNotModifiedButReported(): void
    {
        $sut = $this->getSut();

        $mediaId = uniqid();
        $altText = uniqid();
        $content = '<img'
            . " src=\"{{oeMediaUrl('$mediaId')}}\""
            . " data-id=\"$mediaId\""
            . ' data-source="media"'
            . ' class="dd-wysiwyg-media-image"'
            . ' alt="' . $altText . '">';

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($content, $result->getContent());
        $this->assertCount(1, $result->getCustomAltTextTags());
        $this->assertSame($mediaId, $result->getCustomAltTextTags()[0]['mediaId']);
        $this->assertSame($altText, $result->getCustomAltTextTags()[0]['altText']);
    }

    #[Test]
    public function tagWithoutDataIdIsLeftUnchanged(): void
    {
        $sut = $this->getSut();

        $src = uniqid();
        $content = '<img src="' . $src . '" data-source="media" class="dd-wysiwyg-media-image" alt="">';

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($content, $result->getContent());
        $this->assertSame([], $result->getCustomAltTextTags());
    }

    #[Test]
    public function multipleImagesMixedCases(): void
    {
        $sut = $this->getSut();

        $id1 = uniqid();
        $id2 = uniqid();
        $id3 = uniqid();
        $customAlt = uniqid();

        $img1 = '<img src="{{oeMediaUrl(\'' . $id1 . '\')}}"'
            . ' data-id="' . $id1 . '"'
            . ' class="dd-wysiwyg-media-image"'
            . ' alt="">';
        $img1Migrated = '<img src="{{oeMediaUrl(\'' . $id1 . '\')}}"'
            . ' data-id="' . $id1 . '"'
            . ' class="dd-wysiwyg-media-image"'
            . ' alt="{{oeMediaAlt(\'' . $id1 . '\')}}">';
        $img2 = '<img src="{{oeMediaUrl(\'' . $id2 . '\')}}"'
            . ' data-id="' . $id2 . '"'
            . ' class="dd-wysiwyg-media-image"'
            . ' alt="' . $customAlt . '">';
        $img3 = '<img src="{{oeMediaUrl(\'' . $id3 . '\')}}"'
            . ' data-id="' . $id3 . '"'
            . ' class="dd-wysiwyg-media-image"'
            . ' alt="{{oeMediaAlt(\'' . $id3 . '\')}}">';

        $content = 'start ' . $img1 . ' middle ' . $img2 . ' end ' . $img3;
        $expected = 'start ' . $img1Migrated . ' middle ' . $img2 . ' end ' . $img3;

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($expected, $result->getContent());
        $this->assertCount(1, $result->getCustomAltTextTags());
        $this->assertSame($id2, $result->getCustomAltTextTags()[0]['mediaId']);
        $this->assertSame($customAlt, $result->getCustomAltTextTags()[0]['altText']);
    }

    #[Test]
    public function multilineTagIsHandledCorrectly(): void
    {
        $sut = $this->getSut();

        $mediaId = uniqid();
        $content = "<img src=\"{{oeMediaUrl('$mediaId')}}\"
            data-id=\"$mediaId\"
            data-source=\"media\"
            class=\"dd-wysiwyg-media-image\"
            alt=\"\">";
        $expected = "<img src=\"{{oeMediaUrl('$mediaId')}}\"
            data-id=\"$mediaId\"
            data-source=\"media\"
            class=\"dd-wysiwyg-media-image\"
            alt=\"{{oeMediaAlt('$mediaId')}}\">";

        $result = $sut->migrateAltTexts($content);

        $this->assertSame($expected, $result->getContent());
        $this->assertSame([], $result->getCustomAltTextTags());
    }

    #[Test]
    public function stateIsResetBetweenCalls(): void
    {
        $sut = $this->getSut();

        $mediaId = uniqid();
        $altText = uniqid();
        $contentWithCustom = '<img src="{{oeMediaUrl(\'' . $mediaId . '\')}}"'
            . ' data-id="' . $mediaId . '"'
            . ' class="dd-wysiwyg-media-image"'
            . ' alt="' . $altText . '">';

        $result1 = $sut->migrateAltTexts($contentWithCustom);
        $this->assertCount(1, $result1->getCustomAltTextTags());

        $result2 = $sut->migrateAltTexts('no media here');
        $this->assertSame([], $result2->getCustomAltTextTags());
    }

    private function getSut(): MediaAltTextMigrationService
    {
        return new MediaAltTextMigrationService();
    }
}
