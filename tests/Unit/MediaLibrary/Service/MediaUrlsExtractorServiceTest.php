<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace MediaLibrary\Service;

use OxidEsales\MediaLibrary\Media\DataType\MediaInterface;
use OxidEsales\MediaLibrary\Media\Exception\MediaNotFoundException;
use OxidEsales\MediaLibrary\Media\Repository\PreloadMediaRepositoryInterface;
use OxidEsales\MediaLibrary\Media\Service\MediaObjectResourceInterface;
use OxidEsales\WysiwygModule\MediaLibrary\Service\MediaIdParserServiceInterface;
use OxidEsales\WysiwygModule\MediaLibrary\Service\MediaUrlsExtractorService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MediaUrlsExtractorServiceTest extends TestCase
{
    #[Test]
    public function getContentMediaUrlsReturnsListOfUrls()
    {
        $input = uniqid();

        $mediaParser = $this->createMock(MediaIdParserServiceInterface::class);
        $mediaParser->method('parseMediaIdsFromContent')
            ->with($input)
            ->willReturn([$id1 = uniqid(), $id2 = uniqid()]);

        $preloadExpectation = [$id1, $id2];
        $preloadRepositorySpy = $this->createMock(PreloadMediaRepositoryInterface::class);
        $preloadRepositorySpy->method('registerForPreload')
            ->willReturnCallback(function ($id) use (&$preloadExpectation) {
                $this->assertContains($id, $preloadExpectation);
                unset($preloadExpectation[array_search($id, $preloadExpectation)]);
            });
        $preloadRepositorySpy->method('getMediaById')
            ->willReturnMap([
                [$id1, $media1Stub = $this->createStub(MediaInterface::class)],
                [$id2, $media2Stub = $this->createStub(MediaInterface::class)],
            ]);

        $mediaObjectResourceService = $this->createMock(MediaObjectResourceInterface::class);
        $mediaObjectResourceService->method('getUrlToMedia')
            ->willReturnMap([
                [$media1Stub, $url1 = uniqid()],
                [$media2Stub, $url2 = uniqid()],
            ]);

        $sut = $this->getSut(
            mediaIdParserService: $mediaParser,
            preloadMediaRepository: $preloadRepositorySpy,
            mediaObjectResource: $mediaObjectResourceService,
        );

        $result = $sut->getContentMediaUrls($input);
        $this->assertEquals([$id1 => $url1, $id2 => $url2], $result);

        $this->assertEmpty($preloadExpectation, 'All media IDs should have been registered for preload');
    }

    #[Test]
    public function getContentMediaUrlsReturnsSecondMediaUrlEvenIfFirstDoesntExists()
    {
        $input = uniqid();

        $mediaParser = $this->createMock(MediaIdParserServiceInterface::class);
        $mediaParser->method('parseMediaIdsFromContent')
            ->with($input)
            ->willReturn([$id1 = uniqid(), $id2 = uniqid()]);

        $preloadExpectation = [$id1, $id2];
        $preloadRepositorySpy = $this->createMock(PreloadMediaRepositoryInterface::class);
        $preloadRepositorySpy->method('registerForPreload')
            ->willReturnCallback(function ($id) use (&$preloadExpectation) {
                $this->assertContains($id, $preloadExpectation);
                unset($preloadExpectation[array_search($id, $preloadExpectation)]);
            });

        $media2Stub = $this->createStub(MediaInterface::class);
        $preloadRepositorySpy->method('getMediaById')
            ->willReturnCallback(function (string $mediaId) use ($id2, $media2Stub) {
                if ($mediaId === $id2) {
                    return $media2Stub;
                } else {
                    throw new MediaNotFoundException();
                }
            });

        $mediaObjectResourceService = $this->createMock(MediaObjectResourceInterface::class);
        $mediaObjectResourceService->method('getUrlToMedia')
            ->willReturnMap([
                [$media2Stub, $url2 = uniqid()],
            ]);

        $sut = $this->getSut(
            mediaIdParserService: $mediaParser,
            preloadMediaRepository: $preloadRepositorySpy,
            mediaObjectResource: $mediaObjectResourceService,
        );

        $result = $sut->getContentMediaUrls($input);
        $this->assertEquals([$id1 => '', $id2 => $url2], $result);

        $this->assertEmpty($preloadExpectation, 'All media IDs should have been registered for preload');
    }

    private function getSut(
        MediaIdParserServiceInterface $mediaIdParserService = null,
        PreloadMediaRepositoryInterface $preloadMediaRepository = null,
        MediaObjectResourceInterface $mediaObjectResource = null,
    ): MediaUrlsExtractorService {
        return new MediaUrlsExtractorService(
            mediaIdParserService: $mediaIdParserService,
            preloadMediaRepository: $preloadMediaRepository,
            mediaObjectResource: $mediaObjectResource,
        );
    }
}
