<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace MediaLibrary\Service;

use OxidEsales\MediaLibrary\Media\DataType\MediaLookupContextInterface;
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

        $mediaParserMock = $this->createMock(MediaIdParserServiceInterface::class);
        $mediaParserMock->method('parseMediaIdsFromContent')
            ->with($input)
            ->willReturn([$id1 = uniqid(), $id2 = uniqid()]);

        $preloadExpectation = [$id1, $id2];
        $mediaFacadeSpy = $this->createMock(MediaFacadeInterface::class);
        $mediaFacadeSpy->expects($this->once())
            ->method('registerForPreload')
            ->with(...$preloadExpectation);

        $urlMap = [
            $id1 => $url1 = uniqid(),
            $id2 => $url2 = uniqid()
        ];
        $mediaFacadeSpy->method('getMediaUrl')
            ->willReturnCallback(fn(string $mediaId): string => $urlMap[$mediaId]);

        $sut = $this->getSut(
            mediaIdParserService: $mediaParserMock,
            mediaFacade: $mediaFacadeSpy,
        );

        $result = $sut->getContentMediaUrls($input);
        $this->assertEquals([$id1 => $url1, $id2 => $url2], $result);
    }

    #[Test]
    public function getContentMediaUrlsReturnsSecondMediaUrlEvenIfFirstDoesntExists()
    {
        $input = uniqid();

        $mediaParserMock = $this->createMock(MediaIdParserServiceInterface::class);
        $mediaParserMock->method('parseMediaIdsFromContent')
            ->with($input)
            ->willReturn([$id1 = uniqid(), $id2 = uniqid()]);

        $url2 = uniqid();

        $mediaFacadeMock = $this->createMock(MediaFacadeInterface::class);
        $mediaFacadeMock->method('getMediaUrl')
            ->willReturnCallback(function (string $mediaId) use ($id2, $url2): string {
                if ($mediaId == $id2) {
                    return $url2;
                }

                throw new MediaNotFoundException();
            });

        $sut = $this->getSut(
            mediaIdParserService: $mediaParserMock,
            mediaFacade: $mediaFacadeMock,
        );

        $result = $sut->getContentMediaUrls($input);
        $this->assertEquals([$id1 => '', $id2 => $url2], $result);
    }

    #[Test]
    public function getContentMediaUrlsCallsFacadeWithCorrectContexts()
    {
        $input = uniqid();

        $mediaParserMock = $this->createMock(MediaIdParserServiceInterface::class);
        $mediaParserMock->method('parseMediaIdsFromContent')
            ->with($input)
            ->willReturn([$id1 = uniqid(), $id2 = uniqid()]);

        $mediaFacadeSpy = $this->createMock(MediaFacadeInterface::class);
        $mediaFacadeSpy->expects($this->exactly(2))
            ->method('getMediaUrl')
            ->willReturnCallback(
                function (string $mediaId, MediaLookupContextInterface $context): string {
                    $this->assertSame('Wysiwyg/MediaUrlsExtractor', $context->getTrigger());

                    // identifier should be empty for context, as we cannot say what object it is coming from
                    $this->assertSame('', $context->getIdentifier());

                    return uniqid();
                }
            );

        $sut = $this->getSut(
            mediaIdParserService: $mediaParserMock,
            mediaFacade: $mediaFacadeSpy,
        );

        $sut->getContentMediaUrls($input);
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
