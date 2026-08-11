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
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
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
        ];

        yield 'some images but not the ones we want' => [
            'original' => $random . ' <img src="someurl"> ' . $random,
        ];

        yield 'multiline regular data' => [
            'original' => $random . ' <img src="someurl">
                ' . $random . ' <img src="someotherurl">',
        ];

        yield 'image whose ddmedia path is only in data-filepath, not src' => [
            'original' => $random . ' <img src="https://external.example/pic.jpg"'
                . ' data-filepath="/out/pictures/ddmedia/pic.jpg"> ' . $random,
        ];

        yield 'ordinary page link left untouched' => [
            'original' => $random . ' <a href="/en/some-page" class="link">go</a> ' . $random,
        ];
    }

    #[Test]
    #[DataProvider('noMigrationDataProvider')]
    public function migrationDoesntChangeAnythingForCasesWeAreNotInterestedIn(string $original): void
    {
        $facadeSpy = $this->createMock(MediaIdByPathFacadeInterface::class);
        $facadeSpy->expects($this->never())->method('getMediaIdByPath');

        $sut = $this->getSut(mediaIdByPathFacade: $facadeSpy);

        $result = $sut->migrateContentWithReferences($original);

        $this->assertSame($original, $result->getContent());
        $this->assertSame([], $result->getReferences());
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

        $facadeMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $facadeMock->method('getMediaIdByPath')
            ->with($randomSrc)
            ->willReturn($calculatedMediaId);

        $sut = $this->getSut(mediaIdByPathFacade: $facadeMock);

        $result = $sut->migrateContentWithReferences($input);

        $this->assertSame($expected, $result->getContent());
    }

    #[Test]
    public function migrateContentReturnsTheMigratedContentOnly(): void
    {
        $calculatedMediaId = uniqid();

        $facadeMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $facadeMock->method('getMediaIdByPath')->willReturn($calculatedMediaId);

        $sut = $this->getSut(mediaIdByPathFacade: $facadeMock);

        $result = $sut->migrateContent('start <img src="/out/pictures/ddmedia/1.jpg"> end');

        $this->assertSame(
            'start <img src="{{oeMediaUrl(\'' . $calculatedMediaId . '\')}}" data-id="' . $calculatedMediaId . '"> end',
            $result
        );
    }

    #[Test]
    public function migrateReportsTheConvertedReference(): void
    {
        $calculatedMediaId = uniqid();
        $src = '/out/pictures/ddmedia/1.jpg';

        $facadeMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $facadeMock->method('getMediaIdByPath')->willReturn($calculatedMediaId);

        $sut = $this->getSut(mediaIdByPathFacade: $facadeMock);

        $references = $sut->migrateContentWithReferences('start <img src="' . $src . '"> end')->getReferences();

        $this->assertCount(1, $references);
        $this->assertSame('src', $references[0]->getAttribute());
        $this->assertSame($src, $references[0]->getPath());
        $this->assertSame(MigrationOutcome::Converted, $references[0]->getOutcome());
        $this->assertSame($calculatedMediaId, $references[0]->getMediaId());
        $this->assertSame('', $references[0]->getDetail());
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

        $facadeMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $facadeMock->expects($this->once())
            ->method('getMediaIdByPath')
            ->with($src)
            ->willReturn($calculatedMediaId);

        $sut = $this->getSut(mediaIdByPathFacade: $facadeMock);

        $result = $sut->migrateContentWithReferences($input);

        $this->assertSame($expected, $result->getContent());
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

        $facadeMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $facadeMock->expects($this->once())
            ->method('getMediaIdByPath')
            ->with($href)
            ->willReturn($calculatedMediaId);

        $sut = $this->getSut(mediaIdByPathFacade: $facadeMock);

        $result = $sut->migrateContentWithReferences($input);

        $this->assertSame($expected, $result->getContent());
        $this->assertSame('href', $result->getReferences()[0]->getAttribute());
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

        $facadeMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $facadeMock->method('getMediaIdByPath')
            ->with($randomSrc)
            ->willReturn($calculatedMediaId);

        $sut = $this->getSut(mediaIdByPathFacade: $facadeMock);

        $result = $sut->migrateContentWithReferences($input);

        $this->assertSame($expected, $result->getContent());
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

        $facadeMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $facadeMock->method('getMediaIdByPath')
            ->willReturnMap([
                [$randomSrc1, $calculatedMediaId1],
                [$randomSrc2, $calculatedMediaId2],
            ]);

        $sut = $this->getSut(mediaIdByPathFacade: $facadeMock);

        $result = $sut->migrateContentWithReferences($input);

        $this->assertSame($expected, $result->getContent());
        $this->assertCount(2, $result->getReferences());
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
        $facadeMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $facadeMock->method('getMediaIdByPath')
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

        $sut = $this->getSut(mediaIdByPathFacade: $facadeMock);

        $result = $sut->migrateContentWithReferences($input);

        $this->assertSame($expected, $result->getContent());
    }

    public static function exceptionCasesDataProvider(): \Generator
    {
        yield 'path format unrecognized' => [
            'exceptionStub' => new UnknownPathFormatException('unknown path'),
            'expectedDetail' => 'not recognized as a media library path',
        ];

        yield 'media not found by information' => [
            'exceptionStub' => new MediaNotFoundByFileInformationException("Media 'x.jpg' not found in folder ''"),
            'expectedDetail' => 'no matching entry in the media library',
        ];
    }

    #[Test]
    #[DataProvider('exceptionCasesDataProvider')]
    public function migrateToMediaIdAnchorsLogsAndReportsMediaNotFoundException(
        \Exception $exceptionStub,
        string $expectedDetail
    ): void {
        $randomSrc = uniqid();

        // phpcs:disable
        $input = 'some start <img src="' . $randomSrc . '" style="max-width: 100%;" data-filename="237-536x354.jpg" data-filepath="//localhost.local/out/pictures/ddmedia/237-536x354.jpg" data-source="media" class="dd-wysiwyg-media-image"> some end';
        // phpcs:enable

        $facadeMock = $this->createMock(MediaIdByPathFacadeInterface::class);
        $facadeMock->method('getMediaIdByPath')
            ->with($randomSrc)
            ->willThrowException($exceptionStub);

        $loggerSpy = $this->createMock(LoggerInterface::class);
        $loggerSpy->expects($this->once())
            ->method('warning')
            ->with($exceptionStub->getMessage());

        $sut = $this->getSut(mediaIdByPathFacade: $facadeMock, logger: $loggerSpy);

        $references = $sut->migrateContentWithReferences($input)->getReferences();

        $this->assertCount(1, $references);
        $this->assertSame(MigrationOutcome::Failed, $references[0]->getOutcome());
        $this->assertSame($expectedDetail, $references[0]->getDetail());
        $this->assertSame('', $references[0]->getMediaId());
    }

    private function getSut(
        ?MediaIdByPathFacadeInterface $mediaIdByPathFacade = null,
        ?LoggerInterface $logger = null,
    ): MigrationServiceInterface {
        return new MediaIdAnchorMigrationService(
            mediaIdByPathFacade: $mediaIdByPathFacade ?? $this->createStub(MediaIdByPathFacadeInterface::class),
            logger: $logger ?? $this->createStub(LoggerInterface::class),
        );
    }
}
