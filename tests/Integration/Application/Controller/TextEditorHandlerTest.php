<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Application\Controller;

use _PHPStan_156ee64ba\Symfony\Component\String\UnicodeString;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\WysiwygModule\Application\Controller\TextEditorHandler;
use OxidEsales\WysiwygModule\Service\EditorRendererInterface;

class TextEditorHandlerTest extends IntegrationTestCase
{
    public function testRendererCalledWithInputParameters(): void
    {
        $sut = $this->createPartialMock(TextEditorHandler::class, ['getWysiwywEditorRenderer']);
        $sut->method('getWysiwywEditorRenderer')->willReturn(
            $rendererSpy = $this->createMock(EditorRendererInterface::class)
        );

        $width = 123;
        $height = 321;
        $expectedValue = 'someValue';
        $fieldName = 'someFieldName';

        $objectValue = new UnicodeString($expectedValue);

        $rendererSpy->method('render')->with($width, $height, $expectedValue, $fieldName)
            ->willReturn($expectedResult = uniqid());

        $this->assertSame(
            $expectedResult,
            $sut->renderRichTextEditor(
                width: $width,
                height: $height,
                objectValue: $objectValue,
                fieldName: $fieldName
            )
        );
    }

    public function testGetRenderer(): void
    {
        $sut = $this->createPartialMock(TextEditorHandler::class, []);
        $this->assertInstanceOf(EditorRendererInterface::class, $sut->getWysiwywEditorRenderer());
    }
}
