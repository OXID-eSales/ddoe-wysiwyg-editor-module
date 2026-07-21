<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Service;

use OxidEsales\MediaLibrary\Compatibility\DTO\MediaResolution;
use OxidEsales\MediaLibrary\Compatibility\Exception\MediaNotFoundByFileInformationException;
use OxidEsales\MediaLibrary\Compatibility\Exception\UnknownPathFormatException;
use OxidEsales\MediaLibrary\Compatibility\Service\MediaByPathImportServiceInterface;
use OxidEsales\WysiwygModule\Migration\Service\MigrationReportInterface;
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

        yield 'image whose ddmedia path is only in data-filepath, not src' => [
            'original' => $random . ' <img src="https://external.example/pic.jpg"'
                . ' data-filepath="/out/pictures/ddmedia/pic.jpg"> ' . $random,
            'expected' => $random . ' <img src="https://external.example/pic.jpg"'
                . ' data-filepath="/out/pictures/ddmedia/pic.jpg"> ' . $random,
        ];

        yield 'ordinary page link left untouched' => [
            'original' => $random . ' <a href="/en/some-page" class="link">go</a> ' . $random,
            'expected' => $random . ' <a href="/en/some-page" class="link">go</a> ' . $random,
        ];
    }

    #[Test]
    #[DataProvider('noMigrationDataProvider')]
    public function migrationDoesntChangeAnythingForCasesWeAreNotInterestedIn(string $original, string $expected): void
    {
        $importServiceSpy = $this->createMock(MediaByPathImportServiceInterface::class);
        $importServiceSpy->expects($this->never())->method('getOrImportMedia');

        $sut = $this->getSut(mediaByPathImportService: $importServiceSpy);
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

        $importServiceMock = $this->createMock(MediaByPathImportServiceInterface::class);
        $importServiceMock->method('getOrImportMedia')
            ->with($randomSrc)
            ->willReturn(new MediaResolution($calculatedMediaId, false));

        $sut = $this->getSut(
            mediaByPathImportService: $importServiceMock,
        );

        $result = $sut->migrateContent($input);
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function migrateToMediaIdAnchorsChangesMarkerlessMediaLibraryImagesBySrc(): void
    {
        $calculatedMediaId = uniqid();
        $src = '/out/pictures/ddmedia/1.jpg';

        // phpcs:disable
        $input = 'start <img src="' . $src . '" class="card-img card-img-full" alt="Der Sportliche" width="900" height="900"> end';
        $expected = 'start <img src="{{oeMediaUrl(\'' . $calculatedMediaId . '\')}}" data-id="' . $calculatedMediaId . '" class="card-img card-img-full" alt="Der Sportliche" width="900" height="900"> end';
        // phpcs:enable

        $importServiceMock = $this->createMock(MediaByPathImportServiceInterface::class);
        $importServiceMock->expects($this->once())
            ->method('getOrImportMedia')
            ->with($src)
            ->willReturn(new MediaResolution($calculatedMediaId, true));

        $sut = $this->getSut(
            mediaByPathImportService: $importServiceMock,
        );

        $result = $sut->migrateContent($input);
        $this->assertSame($expected, $result);
    }

    #[Test]
    public function migrateConvertsImageLinkAnchorsByHref(): void
    {
        $calculatedMediaId = uniqid();
        $href = '/out/pictures/ddmedia/1.jpg';

        // phpcs:disable
        $input = 'gallery <a href="' . $href . '" class="stretched-link" target="_blank" data-toggle="lightbox" aria-label="Bild 1"></a> end';
        $expected = 'gallery <a href="{{oeMediaUrl(\'' . $calculatedMediaId . '\')}}" data-id="' . $calculatedMediaId . '" class="stretched-link" target="_blank" data-toggle="lightbox" aria-label="Bild 1"></a> end';
        // phpcs:enable

        $importServiceMock = $this->createMock(MediaByPathImportServiceInterface::class);
        $importServiceMock->expects($this->once())
            ->method('getOrImportMedia')
            ->with($href)
            ->willReturn(new MediaResolution($calculatedMediaId, true));

        $sut = $this->getSut(
            mediaByPathImportService: $importServiceMock,
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

        $importServiceMock = $this->createMock(MediaByPathImportServiceInterface::class);
        $importServiceMock->method('getOrImportMedia')
            ->with($randomSrc)
            ->willReturn(new MediaResolution($calculatedMediaId, false));

        $sut = $this->getSut(
            mediaByPathImportService: $importServiceMock,
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

        $importServiceMock = $this->createMock(MediaByPathImportServiceInterface::class);
        $importServiceMock->method('getOrImportMedia')
            ->willReturnMap([
                [$randomSrc1, new MediaResolution($calculatedMediaId1, false)],
                [$randomSrc2, new MediaResolution($calculatedMediaId2, false)],
            ]);

        $sut = $this->getSut(
            mediaByPathImportService: $importServiceMock,
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
        $importServiceMock = $this->createMock(MediaByPathImportServiceInterface::class);
        $importServiceMock->method('getOrImportMedia')
            ->willReturnCallback(function ($path) use (
                $randomSrc1,
                $calculatedMediaId1,
                $exception,
            ) {
                if ($path === $randomSrc1) {
                    return new MediaResolution($calculatedMediaId1, false);
                }

                throw $exception;
            });

        $sut = $this->getSut(
            mediaByPathImportService: $importServiceMock,
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

        $importServiceMock = $this->createMock(MediaByPathImportServiceInterface::class);
        $importServiceMock->method('getOrImportMedia')
            ->with($randomSrc)
            ->willThrowException($exceptionStub);

        $loggerSpy = $this->createMock(LoggerInterface::class);
        $loggerSpy->expects($this->once())
            ->method('warning')
            ->with($exceptionStub->getMessage());

        $sut = $this->getSut(
            mediaByPathImportService: $importServiceMock,
            logger: $loggerSpy,
        );

        $sut->migrateContent($input);
    }

    private function getSut(
        MediaByPathImportServiceInterface $mediaByPathImportService = null,
        MigrationReportInterface $report = null,
        LoggerInterface $logger = null,
    ): MigrationServiceInterface {
        $mediaByPathImportService ??= $this->createStub(MediaByPathImportServiceInterface::class);
        $report ??= $this->createStub(MigrationReportInterface::class);
        $logger ??= $this->createStub(LoggerInterface::class);

        return new MediaIdAnchorMigrationService(
            mediaByPathImportService: $mediaByPathImportService,
            report: $report,
            logger: $logger,
        );
    }
}
