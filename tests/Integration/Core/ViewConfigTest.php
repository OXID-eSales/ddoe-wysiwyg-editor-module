<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\WysiwygModule\Tests\Integration\Core;

use OxidEsales\VisualCmsModule\Tests\Integration\IntegrationTestCase;
use OxidEsales\WysiwygModule\Service\Media;

/**
 * Class ddVisualEditorOxViewConfigTest
 */
class ViewConfigTest extends IntegrationTestCase
{
    public function testGetMediaUrl(): void
    {
        $mediaMock = $this->createPartialMock(Media::class, ['getMediaUrl']);
        $mediaMock->method('getMediaUrl')->with('someFile')->willReturn('someFilePath');

        $sut = $this->createPartialMock(
            \OxidEsales\VisualCmsModule\Core\ViewConfig::class,
            ['getServiceFromContainer']
        );
        $sut->expects($this->any())->method('getServiceFromContainer')
            ->with(Media::class)
            ->willReturn($mediaMock);

        $this->assertSame('someFilePath', $sut->getMediaUrl('someFile'));
    }
}
