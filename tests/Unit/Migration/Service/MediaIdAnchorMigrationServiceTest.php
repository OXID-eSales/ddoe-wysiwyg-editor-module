<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Service;

use OxidEsales\MediaLibrary\Compatibility\DTO\MediaFileInformationInterface;
use OxidEsales\MediaLibrary\Compatibility\Exception\UnknownPathFormatException;
use OxidEsales\MediaLibrary\Compatibility\Factory\MediaFileInformationFactoryInterface;
use OxidEsales\MediaLibrary\Compatibility\Repository\PathMappingRepositoryInterface;
use OxidEsales\WysiwygModule\Migration\Service\MediaIdAnchorMigrationService;
use OxidEsales\WysiwygModule\Migration\Service\MigrationServiceInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MediaIdAnchorMigrationServiceTest extends TestCase
{
    public static function noMigrationDataProvider(): \Generator
    {
        $random = uniqid();

        yield 'no media anchor' => [
            'original' => $random,
            'expected' => $random,
        ];

        yield 'some images but not the ones we want' => [
            'original' => $random . ' <img src="someurl"> ' . $random,
            'expected' => $random . ' <img src="someurl"> ' . $random,
        ];

        yield 'multiline regular data' => [
            'original' => $random . ' <img src="someurl"> 
                ' . $random . ' <img src="someotherurl">',
            'expected' => $random . ' <img src="someurl"> 
                ' . $random . ' <img src="someotherurl">',
        ];
    }

    #[Test]
    #[DataProvider('noMigrationDataProvider')]
    public function migrationDoesntChangeAnythingForCasesWeAreNotInterestedIn(string $original, string $expected): void
    {
        $sut = $this->getSut();
        $result = $sut->migrateContent($original);
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function migrateToMediaIdAnchorsChangesTagsCorrectly(): void
    {
        $calculatedMediaId = uniqid();

        $randomSrc = uniqid();

        // phpcs:disable
        $input = 'some start <img src="' . $randomSrc . '" style="max-width: 100%;" data-filename="237-536x354.jpg" data-filepath="//localhost.local/out/pictures/ddmedia/237-536x354.jpg" data-source="media" class="dd-wysiwyg-media-image"> some end';
        $expected = 'some start <img src="{{oeMediaUrl(\'' . $calculatedMediaId . '\')}}" data-id="' . $calculatedMediaId . '" style="max-width: 100%;" data-source="media" class="dd-wysiwyg-media-image"> some end';
        // phpcs:enable

        $mediaFileInformationFactoryMock = $this->createMock(MediaFileInformationFactoryInterface::class);
        $mediaFileInformationFactoryMock->method('fromPath')
            ->with($randomSrc)
            ->willReturn($mediaInformationStub = $this->createStub(MediaFileInformationInterface::class));

        $pathMappingRepositoryMock = $this->createMock(PathMappingRepositoryInterface::class);
        $pathMappingRepositoryMock->method('getMediaIdByInformation')
            ->with($mediaInformationStub)
            ->willReturn($calculatedMediaId);

        $sut = new MediaIdAnchorMigrationService(
            mediaFileInformationFactory: $mediaFileInformationFactoryMock,
            pathMappingRepository: $pathMappingRepositoryMock,
        );

        $result = $sut->migrateContent($input);
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function migrateToMediaIdAnchorsChangesTagsCorrectlyForMultilineImgTag(): void
    {
        $calculatedMediaId = uniqid();

        // phpcs:disable
        $randomSrc = uniqid();
        $input = 'some start <img src="' . $randomSrc . '"
            style="max-width: 100%;" data-filename="237-536x354.jpg"
            data-filepath="//localhost.local/out/pictures/ddmedia/237-536x354.jpg"
            data-source="media" class="dd-wysiwyg-media-image"> some end';
        $expected = 'some start <img src="{{oeMediaUrl(\'' . $calculatedMediaId . '\')}}" data-id="' . $calculatedMediaId . '"
            style="max-width: 100%;"
            data-source="media" class="dd-wysiwyg-media-image"> some end';
        // phpcs:enable

        $mediaFileInformationFactoryMock = $this->createMock(MediaFileInformationFactoryInterface::class);
        $mediaFileInformationFactoryMock->method('fromPath')
            ->with($randomSrc)
            ->willReturn($mediaInformationStub = $this->createStub(MediaFileInformationInterface::class));

        $pathMappingRepositoryMock = $this->createMock(PathMappingRepositoryInterface::class);
        $pathMappingRepositoryMock->method('getMediaIdByInformation')
            ->with($mediaInformationStub)
            ->willReturn($calculatedMediaId);

        $sut = new MediaIdAnchorMigrationService(
            mediaFileInformationFactory: $mediaFileInformationFactoryMock,
            pathMappingRepository: $pathMappingRepositoryMock,
        );

        $result = $sut->migrateContent($input);
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function migrateToMediaIdAnchorsChangesTagsCorrectlyInMoreComplicatedMultipleImagesCase(): void
    {
        $calculatedMediaId1 = uniqid();
        $calculatedMediaId2 = uniqid();

        $randomSrc1 = uniqid();
        $randomSrc2 = uniqid();

        // phpcs:disable
        $input = 'some start <img src="' . $randomSrc1 . '"
            style="max-width: 100%;" data-filename="' . uniqid() . '"
            data-filepath="' . uniqid() . '"
            data-source="media" class="dd-wysiwyg-media-image"> some middle<img src="some random image"> and second media
            <img src="' . $randomSrc2 . '"
            style="max-width: 100%;" data-filename="' . uniqid() . '" data-filepath="' . uniqid() . '" data-source="media"
            class="dd-wysiwyg-media-image">the end';
        $expected = 'some start <img src="{{oeMediaUrl(\'' . $calculatedMediaId1 . '\')}}" data-id="' . $calculatedMediaId1 . '"
            style="max-width: 100%;"
            data-source="media" class="dd-wysiwyg-media-image"> some middle<img src="some random image"> and second media
            <img src="{{oeMediaUrl(\'' . $calculatedMediaId2 . '\')}}" data-id="' . $calculatedMediaId2 . '"
            style="max-width: 100%;" data-source="media"
            class="dd-wysiwyg-media-image">the end';
        // phpcs:enable

        $mediaFileInformationFactoryMock = $this->createMock(MediaFileInformationFactoryInterface::class);
        $mediaFileInformationFactoryMock->method('fromPath')->willReturnMap([
            [$randomSrc1, $mediaInformationStub1 = $this->createStub(MediaFileInformationInterface::class)],
            [$randomSrc2, $mediaInformationStub2 = $this->createStub(MediaFileInformationInterface::class)],
        ]);

        $pathMappingRepositoryMock = $this->createMock(PathMappingRepositoryInterface::class);
        $pathMappingRepositoryMock->method('getMediaIdByInformation')->willReturnMap([
            [$mediaInformationStub1, $calculatedMediaId1],
            [$mediaInformationStub2, $calculatedMediaId2],
        ]);

        $sut = new MediaIdAnchorMigrationService(
            mediaFileInformationFactory: $mediaFileInformationFactoryMock,
            pathMappingRepository: $pathMappingRepositoryMock,
        );

        $result = $sut->migrateContent($input);
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function migrateToMediaIdAnchorsDoesNotChangeTheItemIfPathCannotBeRecognised(): void
    {
        $calculatedMediaId1 = uniqid();

        $randomSrc1 = uniqid();
        $randomSrc2 = uniqid();

        // phpcs:disable
        $input = 'some start <img src="' . $randomSrc1 . '"
            style="max-width: 100%;" data-filename="' . uniqid() . '"
            data-filepath="' . uniqid() . '"
            data-source="media" class="dd-wysiwyg-media-image"> some middle<img src="some random image"> and second media
            <img src="' . $randomSrc2 . '"
            style="max-width: 100%;" data-filename="somethinghere" data-filepath="andhere" data-source="media"
            class="dd-wysiwyg-media-image">the end';
        $expected = 'some start <img src="{{oeMediaUrl(\'' . $calculatedMediaId1 . '\')}}" data-id="' . $calculatedMediaId1 . '"
            style="max-width: 100%;"
            data-source="media" class="dd-wysiwyg-media-image"> some middle<img src="some random image"> and second media
            <img src="' . $randomSrc2 . '"
            style="max-width: 100%;" data-filename="somethinghere" data-filepath="andhere" data-source="media"
            class="dd-wysiwyg-media-image">the end';
        // phpcs:enable

        // second src will not be recognised as valid path
        $mediaInformationStub1 = $this->createStub(MediaFileInformationInterface::class);
        $mediaFileInformationFactoryMock = $this->createMock(MediaFileInformationFactoryInterface::class);
        $mediaFileInformationFactoryMock->method('fromPath')->willReturnCallback(function ($path) use (
            $randomSrc1,
            $mediaInformationStub1,
        ) {
            if ($path === $randomSrc1) {
                return $mediaInformationStub1;
            }
            throw new UnknownPathFormatException();
        });

        $pathMappingRepositoryMock = $this->createMock(PathMappingRepositoryInterface::class);
        $pathMappingRepositoryMock->method('getMediaIdByInformation')->willReturnMap([
            [$mediaInformationStub1, $calculatedMediaId1],
        ]);

        $sut = new MediaIdAnchorMigrationService(
            mediaFileInformationFactory: $mediaFileInformationFactoryMock,
            pathMappingRepository: $pathMappingRepositoryMock,
        );

        $result = $sut->migrateContent($input);
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function migrateToMediaIdAnchorsDoesNotChangeTheItemIfMediaNotFound(): void
    {
        $calculatedMediaId1 = uniqid();

        $randomSrc1 = uniqid();
        $randomSrc2 = uniqid();

        // phpcs:disable
        $input = 'some start <img src="' . $randomSrc1 . '"
            style="max-width: 100%;" data-filename="' . uniqid() . '"
            data-filepath="' . uniqid() . '"
            data-source="media" class="dd-wysiwyg-media-image"> some middle<img src="some random image"> and second media
            <img src="' . $randomSrc2 . '"
            style="max-width: 100%;" data-filename="somethinghere" data-filepath="andhere" data-source="media"
            class="dd-wysiwyg-media-image">the end';
        $expected = 'some start <img src="{{oeMediaUrl(\'' . $calculatedMediaId1 . '\')}}" data-id="' . $calculatedMediaId1 . '"
            style="max-width: 100%;"
            data-source="media" class="dd-wysiwyg-media-image"> some middle<img src="some random image"> and second media
            <img src="' . $randomSrc2 . '"
            style="max-width: 100%;" data-filename="somethinghere" data-filepath="andhere" data-source="media"
            class="dd-wysiwyg-media-image">the end';
        // phpcs:enable

        // second src will not be recognised as valid path
        $mediaInformationStub1 = $this->createStub(MediaFileInformationInterface::class);
        $mediaFileInformationFactoryMock = $this->createMock(MediaFileInformationFactoryInterface::class);
        $mediaFileInformationFactoryMock->method('fromPath')->willReturnCallback(function ($path) use (
            $randomSrc1,
            $mediaInformationStub1,
        ) {
            if ($path === $randomSrc1) {
                return $mediaInformationStub1;
            }
            throw new UnknownPathFormatException();
        });

        $pathMappingRepositoryMock = $this->createMock(PathMappingRepositoryInterface::class);
        $pathMappingRepositoryMock->method('getMediaIdByInformation')->willReturnMap([
            [$mediaInformationStub1, $calculatedMediaId1],
        ]);

        $sut = new MediaIdAnchorMigrationService(
            mediaFileInformationFactory: $mediaFileInformationFactoryMock,
            pathMappingRepository: $pathMappingRepositoryMock,
        );

        $result = $sut->migrateContent($input);
        $this->assertSame($expected, $result);
    }

    private function getSut(
        MediaFileInformationFactoryInterface $mediaFileInformationFactory = null,
        PathMappingRepositoryInterface $pathMappingRepository = null,
    ): MigrationServiceInterface {
        $mediaFileInformationFactory ??= $this->createStub(MediaFileInformationFactoryInterface::class);
        $pathMappingRepository ??= $this->createStub(PathMappingRepositoryInterface::class);

        return new MediaIdAnchorMigrationService(
            mediaFileInformationFactory: $mediaFileInformationFactory,
            pathMappingRepository: $pathMappingRepository,
        );
    }
}
