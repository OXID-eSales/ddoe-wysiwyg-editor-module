<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Integration;

use OxidEsales\EshopCommunity\Internal\Container\ContainerBuilderFactory;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class ServiceAvailabilityTest extends IntegrationTestCase
{
    private static $cachedContainer;
    private static $decorations;

    public static function setUpBeforeClass(): void
    {
        $containerBuilder = (new ContainerBuilderFactory())->create();
        $container = $containerBuilder->getContainer();
        foreach ($container->getDefinitions() as $id => $definition) {
            $definition->setPublic(true);
            if ($decorated = $definition->getDecoratedService()) {
                self::$decorations[reset($decorated)][] = $id;
            }
        }
        $container->compile(true);

        self::$cachedContainer = $container;
    }

    #[DataProvider('serviceAvailabilityDataProvider')]
    #[Test]
    public function servicesAvailable(string $serviceName): void
    {
        $service = self::$cachedContainer->get($serviceName);
        $this->assertInstanceOf($serviceName, $service);
    }

    #[DataProvider('serviceDecorationProvider')]
//    #[Test]
    public function servicesDecorated(string $serviceName, array $expectedDecorations): void
    {
        $decorations = self::$decorations[$serviceName];
        foreach ($expectedDecorations as $oneExpectedDecoration) {
            $this->assertContains($oneExpectedDecoration, $decorations);
        }
    }

    public static function serviceDecorationProvider(): \Generator
    {
    }

    public static function serviceAvailabilityDataProvider(): array
    {
        return [
            // HtmlFilter
            [\OxidEsales\WysiwygModule\HtmlFilter\HtmlFilterInterface::class],
            [\OxidEsales\WysiwygModule\HtmlFilter\HtmlRemoverInterface::class],

            // MediaLibrary
            [\OxidEsales\WysiwygModule\MediaLibrary\Service\MediaIdParserServiceInterface::class],
            [\OxidEsales\WysiwygModule\MediaLibrary\Service\MediaUrlsExtractorServiceInterface::class],

            // Migration
            [\OxidEsales\WysiwygModule\Migration\Command\MigrateMediaUrlsToIdsCommand::class],

            // Service
            [\OxidEsales\WysiwygModule\Service\EditorRendererInterface::class],
            [\OxidEsales\WysiwygModule\Service\SettingsInterface::class],
        ];
    }
}
