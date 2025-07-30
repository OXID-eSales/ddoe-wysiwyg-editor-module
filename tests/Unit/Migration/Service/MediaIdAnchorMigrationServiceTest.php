<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Service;

use OxidEsales\MediaLibrary\Compatibility\Exception\MediaNotFoundByFileInformationException;
use OxidEsales\MediaLibrary\Compatibility\Exception\UnknownPathFormatException;
use OxidEsales\MediaLibrary\Compatibility\Facade\MediaIdByPathFacadeInterface;
use OxidEsales\WysiwygModule\Migration\Service\MediaIdAnchorMigrationService;
use OxidEsales\WysiwygModule\Migration\Service\MigrationServiceInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

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

        $mediaIdByPathMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $mediaIdByPathMock->method('getMediaIdByPath')
            ->with($randomSrc)
            ->willReturn($calculatedMediaId);

        $sut = $this->getSut(
            mediaIdByPathFacade: $mediaIdByPathMock,
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

        $mediaIdByPathMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $mediaIdByPathMock->method('getMediaIdByPath')
            ->with($randomSrc)
            ->willReturn($calculatedMediaId);

        $sut = $this->getSut(
            mediaIdByPathFacade: $mediaIdByPathMock,
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

        $mediaIdByPathMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $mediaIdByPathMock->method('getMediaIdByPath')
            ->willReturnMap([
                [$randomSrc1, $calculatedMediaId1],
                [$randomSrc2, $calculatedMediaId2],
            ]);

        $sut = $this->getSut(
            mediaIdByPathFacade: $mediaIdByPathMock,
        );

        $result = $sut->migrateContent($input);
        $this->assertSame($expected, $result);
    }

    public static function pathRecognitionIssuesDataProvider(): \Generator
    {
        yield 'path format unrecognized' => [
            'exception' => new UnknownPathFormatException()
        ];

        yield 'media not found by information' => [
            'exception' => new MediaNotFoundByFileInformationException()
        ];
    }

    #[Test]
    #[DataProvider('pathRecognitionIssuesDataProvider')]
    public function migrateToMediaIdAnchorsDoesNotChangeTheItemIfThereArePathRecognitionIssues(
        \Exception $exception
    ): void {
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

        // second src media calculation will throw an exception
        $mediaIdByPathMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $mediaIdByPathMock->method('getMediaIdByPath')
            ->willReturnCallback(function ($path) use (
                $randomSrc1,
                $calculatedMediaId1,
                $exception,
            ) {
                if ($path === $randomSrc1) {
                    return $calculatedMediaId1;
                }

                throw $exception;
            });

        $sut = $this->getSut(
            mediaIdByPathFacade: $mediaIdByPathMock,
        );

        $result = $sut->migrateContent($input);
        $this->assertSame($expected, $result);
    }

    public static function exceptionCasesDataProvider(): \Generator
    {
        yield 'path format unrecognized' => [
            'exceptionStub' => new UnknownPathFormatException(),
        ];

        yield 'media not found by information' => [
            'exceptionStub' => new MediaNotFoundByFileInformationException(),
        ];
    }

    #[Test]
    #[DataProvider('exceptionCasesDataProvider')]
    public function migrateToMediaIdAnchorsLogsMediaNotFoundException(\Exception $exceptionStub): void
    {
        $randomSrc = uniqid();

        // phpcs:disable
        $input = 'some start <img src="' . $randomSrc . '" style="max-width: 100%;" data-filename="237-536x354.jpg" data-filepath="//localhost.local/out/pictures/ddmedia/237-536x354.jpg" data-source="media" class="dd-wysiwyg-media-image"> some end';
        // phpcs:enable

        $mediaIdByPathMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $mediaIdByPathMock->method('getMediaIdByPath')
            ->with($randomSrc)
            ->willThrowException($exceptionStub);

        $loggerSpy = $this->createMock(LoggerInterface::class);
        $loggerSpy->expects($this->once())
            ->method('warning')
            ->with($exceptionStub->getMessage());

        $sut = $this->getSut(
            mediaIdByPathFacade: $mediaIdByPathMock,
            logger: $loggerSpy,
        );

        $sut->migrateContent($input);
    }

    private function getSut(
        MediaIdByPathFacadeInterface $mediaIdByPathFacade = null,
        LoggerInterface $logger = null,
    ): MigrationServiceInterface {
        $mediaIdByPathFacade ??= $this->createStub(MediaIdByPathFacadeInterface::class);
        $logger ??= $this->createStub(LoggerInterface::class);

        return new MediaIdAnchorMigrationService(
            mediaIdByPathFacade: $mediaIdByPathFacade,
            logger: $logger,
        );
    }
}
