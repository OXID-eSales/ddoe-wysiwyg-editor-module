<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace MediaLibrary\Service;

use OxidEsales\MediaLibrary\Media\Exception\MediaNotFoundException;
use OxidEsales\MediaLibrary\Media\Facade\MediaFacadeInterface;
use OxidEsales\WysiwygModule\MediaLibrary\Service\MediaIdParserServiceInterface;
use OxidEsales\WysiwygModule\MediaLibrary\Service\MediaUrlsExtractorService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MediaUrlsExtractorServiceTest extends TestCase
{
    #[Test]
    public function getContentMediaUrlsPreregisterIdsAndReturnsListOfUrls()
    {
        $input = uniqid();

        $mediaParser = $this->createMock(MediaIdParserServiceInterface::class);
        $mediaParser->method('parseMediaIdsFromContent')
            ->with($input)
            ->willReturn([$id1 = uniqid(), $id2 = uniqid()]);

        $preloadExpectation = [$id1, $id2];
        $mediaFacadeMock = $this->createMock(MediaFacadeInterface::class);
        $mediaFacadeMock->expects($this->once())
            ->method('registerForPreload')
            ->with(...$preloadExpectation);
        $mediaFacadeMock->method('getMediaUrl')
            ->willReturnMap([
                [$id1, $url1 = uniqid()],
                [$id2, $url2 = uniqid()],
            ]);

        $sut = $this->getSut(
            mediaIdParserService: $mediaParser,
            mediaFacade: $mediaFacadeMock,
        );

        $result = $sut->getContentMediaUrls($input);
        $this->assertEquals([$id1 => $url1, $id2 => $url2], $result);
    }

    #[Test]
    public function getContentMediaUrlsReturnsSecondMediaUrlEvenIfFirstDoesntExists()
    {
        $input = uniqid();

        $mediaParser = $this->createMock(MediaIdParserServiceInterface::class);
        $mediaParser->method('parseMediaIdsFromContent')
            ->with($input)
            ->willReturn([$id1 = uniqid(), $id2 = uniqid()]);

        $url2 = uniqid();

        $mediaFacadeMock = $this->createMock(MediaFacadeInterface::class);
        $mediaFacadeMock->method('getMediaUrl')
            ->willReturnCallback(function (string $mediaId) use ($id2, $url2) {
                if ($mediaId === $id2) {
                    return $url2;
                } else {
                    throw new MediaNotFoundException();
                }
            });

        $sut = $this->getSut(
            mediaIdParserService: $mediaParser,
            mediaFacade: $mediaFacadeMock,
        );

        $result = $sut->getContentMediaUrls($input);
        $this->assertEquals([$id1 => '', $id2 => $url2], $result);
    }

    private function getSut(
        ?MediaIdParserServiceInterface $mediaIdParserService = null,
        ?MediaFacadeInterface $mediaFacade = null,
    ): MediaUrlsExtractorService {
        $mediaIdParserService ??= $this->createStub(MediaIdParserServiceInterface::class);
        $mediaFacade ??= $this->createStub(MediaFacadeInterface::class);

        return new MediaUrlsExtractorService(
            mediaIdParserService: $mediaIdParserService,
            mediaFacade: $mediaFacade,
        );
    }
}
