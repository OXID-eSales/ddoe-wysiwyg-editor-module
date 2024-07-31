<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Service;

use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\WysiwygModule\Service\Settings;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SettingsTest extends TestCase
{
    public static function isSslDataProvider(): \Generator
    {
        yield [
            'expectedResult' => true,
            'configValues' => [
                ['sSSLShopURL', null, 'someUrl'],
                ['sMallSSLShopURL', null, 'someMallUrl']
            ]
        ];

        yield [
            'expectedResult' => true,
            'configValues' => [
                ['sSSLShopURL', null, 'someUrl'],
            ]
        ];

        yield [
            'expectedResult' => true,
            'configValues' => [
                ['sMallSSLShopURL', null, 'someMallUrl']
            ]
        ];

        yield [
            'expectedResult' => false,
            'configValues' => []
        ];
    }

    #[DataProvider('isSslDataProvider')]
    public function testIsSsl(bool $expectedResult, array $configValues): void
    {
        $sut = $this->getSut(
            shopConfig: $config = $this->createStub(Config::class)
        );
        $config->method('getConfigParam')->willReturnMap($configValues);

        $this->assertEquals($expectedResult, $sut->isSsl());
    }

    public function testGetInterfaceLanguage(): void
    {
        $sut = $this->getSut(
            shopLanguage: $shopLanguageMock = $this->createMock(Language::class)
        );

        $someLanguageId = rand(0, 100);
        $languageAbbreviation = uniqid();
        $shopLanguageMock->method('getTplLanguage')->willReturn($someLanguageId);
        $shopLanguageMock->method('getLanguageAbbr')->with($someLanguageId)->willReturn($languageAbbreviation);

        $this->assertSame($languageAbbreviation, $sut->getInterfaceLanguageAbbreviation());
    }

    public function testGetActiveViewConfig(): void
    {
        $sut = $this->getSut(
            shopConfig: $config = $this->createStub(Config::class)
        );

        $viewStub = $this->createStub(FrontendController::class);
        $viewStub->method('getViewConfig')->willReturn(
            $viewConfigStub = $this->createStub(ViewConfig::class)
        );
        $config->method('getTopActiveView')->willReturn($viewStub);


        $this->assertEquals($viewConfigStub, $sut->getActiveViewConfig());
    }

    protected function getSut(
        Config $shopConfig = null,
        Language $shopLanguage = null,
    ): Settings {
        return new Settings(
            shopConfig: $shopConfig ?? $this->createStub(Config::class),
            shopLanguage: $shopLanguage ?? $this->createStub(Language::class),
        );
    }
}
