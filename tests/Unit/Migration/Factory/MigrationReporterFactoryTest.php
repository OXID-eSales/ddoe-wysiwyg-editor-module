<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Factory;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\WysiwygModule\Migration\Factory\MigrationReporterFactory;
use OxidEsales\WysiwygModule\Migration\Factory\MigrationReporterFactoryInterface;
use OxidEsales\WysiwygModule\Migration\Reporter\CsvFileMigrationReporter;
use OxidEsales\WysiwygModule\Migration\Reporter\ScreenMigrationReporter;
use OxidEsales\WysiwygModule\Migration\Service\MediaMigrationResultFilterInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MigrationReporterFactoryTest extends TestCase
{
    public static function noReportFileDataProvider(): \Generator
    {
        yield 'no option given' => ['reportFilePath' => null];
        yield 'empty option given' => ['reportFilePath' => ''];
    }

    #[Test]
    #[DataProvider('noReportFileDataProvider')]
    public function createReturnsScreenReporterWhenNoReportFileIsRequested(?string $reportFilePath): void
    {
        $sut = $this->getSut();

        $reporter = $sut->create($reportFilePath);

        $this->assertInstanceOf(ScreenMigrationReporter::class, $reporter);
    }

    #[Test]
    public function createReturnsCsvFileReporterWhenAReportFileIsRequested(): void
    {
        $sut = $this->getSut();

        $reporter = $sut->create(uniqid('exampleFilePath'));

        $this->assertInstanceOf(CsvFileMigrationReporter::class, $reporter);
    }

    private function getSut(
        ?ContextInterface $context = null,
        ?MediaMigrationResultFilterInterface $resultFilter = null,
    ): MigrationReporterFactoryInterface {
        $context ??= $this->createStub(ContextInterface::class);
        $resultFilter ??= $this->createStub(MediaMigrationResultFilterInterface::class);

        return new MigrationReporterFactory(
            context: $context,
            resultFilter: $resultFilter,
        );
    }
}
