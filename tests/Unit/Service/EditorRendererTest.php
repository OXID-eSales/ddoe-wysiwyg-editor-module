<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Service;

use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\WysiwygModule\Service\EditorRenderer;
use OxidEsales\WysiwygModule\Service\SettingsInterface;
use PHPUnit\Framework\TestCase;

class EditorRendererTest extends TestCase
{
    public static function renderSizeParamsDataProvider(): \Generator
    {
        yield [
            'width' => '100',
            'height' => '200',
            'expectedWidth' => '100px',
            'expectedHeight' => '200px',
        ];

        yield [
            'width' => '100px',
            'height' => '200px',
            'expectedWidth' => '100px',
            'expectedHeight' => '200px',
        ];

        yield [
            'width' => '100%',
            'height' => '200%',
            'expectedWidth' => '100%',
            'expectedHeight' => '200%',
        ];

        yield [
            'width' => '100x',
            'height' => '200z',
            'expectedWidth' => '100x',
            'expectedHeight' => '200z',
        ];
    }

    public function testRenderReturnsRenderedValue(): void
    {
        $sut = $this->getSut(
            templateRenderer: $templateRendererMock = $this->createMock(TemplateRendererInterface::class)
        );

        $expectedValue = 'someRenderedContent';
        $templateRendererMock->method('renderTemplate')
            ->with('@ddoewysiwyg/ddoewysiwyg', $this->anything())
            ->willReturn($expectedValue);

        $this->assertSame(
            $expectedValue,
            $sut->render('someWidth', 'someHeight', 'someValue', 'someField')
        );
    }

    /** @dataProvider renderSizeParamsDataProvider */
    public function testRenderCalledWithCorrectSizeParams(
        string $width,
        string $height,
        string $expectedWidth,
        string $expectedHeight,
    ): void {
        $sut = $this->getSut(
            templateRenderer: $templateRendererSpy = $this->createMock(TemplateRendererInterface::class)
        );

        $templateRendererSpy
            ->expects($this->once())
            ->method('renderTemplate')
            ->with(
                '@ddoewysiwyg/ddoewysiwyg',
                $this->callback(function ($input) use ($expectedHeight, $expectedWidth) {
                    $this->assertSame($expectedWidth, $input['iEditorWidth']);
                    $this->assertSame($expectedHeight, $input['iEditorHeight']);

                    return true;
                })
            );

        $sut->render($width, $height, 'anything', 'anything');
    }

    public function testRenderCalledWithCorrectInputValues(): void
    {
        $sut = $this->getSut(
            templateRenderer: $templateRendererSpy = $this->createMock(TemplateRendererInterface::class)
        );

        $fieldName = uniqid();
        $fieldValue = uniqid();

        $templateRendererSpy
            ->expects($this->once())
            ->method('renderTemplate')
            ->with(
                '@ddoewysiwyg/ddoewysiwyg',
                $this->callback(function ($input) use ($fieldName, $fieldValue) {
                    $this->assertSame($fieldName, $input['sEditorField']);
                    $this->assertSame($fieldValue, $input['sEditorValue']);

                    return true;
                })
            );

        $sut->render('any', 'any', $fieldValue, $fieldName);
    }

    public function testRenderCalledWithCorrectLanguage(): void
    {
        $sut = $this->getSut(
            templateRenderer: $templateRendererSpy = $this->createMock(TemplateRendererInterface::class),
            settingsService: $settingsServiceStub = $this->createStub(SettingsInterface::class),
        );

        $settingsServiceStub->method('getInterfaceLanguageAbbreviation')->willReturn($language = uniqid());

        $templateRendererSpy
            ->expects($this->once())
            ->method('renderTemplate')
            ->with(
                '@ddoewysiwyg/ddoewysiwyg',
                $this->callback(function ($input) use ($language) {
                    $this->assertSame($language, $input['langabbr']);

                    return true;
                })
            );

        $sut->render('any', 'any', 'any', 'any');
    }

    public static function activityFlagDataProvider(): \Generator
    {
        yield [
            'flagValue' => true,
        ];

        yield [
            'flagValue' => false,
        ];
    }

    /** @dataProvider activityFlagDataProvider */
    public function testRenderCalledWithCorrectTextareaActivityFlag(bool $flagValue): void
    {
        $sut = $this->getSut(
            templateRenderer: $templateRendererSpy = $this->createMock(TemplateRendererInterface::class)
        );

        $templateRendererSpy
            ->expects($this->once())
            ->method('renderTemplate')
            ->with(
                '@ddoewysiwyg/ddoewysiwyg',
                $this->callback(function ($input) use ($flagValue) {
                    $this->assertSame($flagValue, $input['blTextEditorDisabled']);
                    return true;
                })
            );

        $sut->render('any', 'any', 'any', 'any', $flagValue);
    }

    public function testRenderCalledWithActiveViewConfig(): void
    {
        $sut = $this->getSut(
            templateRenderer: $templateRendererSpy = $this->createMock(TemplateRendererInterface::class),
            settingsService: $settingsServiceStub = $this->createStub(SettingsInterface::class),
        );

        $viewConfigStub = $this->createStub(ViewConfig::class);
        $settingsServiceStub->method('getActiveViewConfig')->willReturn($viewConfigStub);

        $templateRendererSpy
            ->expects($this->once())
            ->method('renderTemplate')
            ->with(
                '@ddoewysiwyg/ddoewysiwyg',
                $this->callback(function ($input) use ($viewConfigStub) {
                    $this->assertSame($viewConfigStub, $input['oViewConf']);
                    return true;
                })
            );

        $sut->render('any', 'any', 'any', 'any');
    }

    public function getSut(
        TemplateRendererInterface $templateRenderer = null,
        SettingsInterface $settingsService = null,
    ): EditorRenderer {
        return new EditorRenderer(
            templateRenderer: $templateRenderer ?? $this->createStub(TemplateRendererInterface::class),
            settingsService: $settingsService ?? $this->createStub(SettingsInterface::class),
        );
    }
}
