<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Integration\Migration\Factory;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use OxidEsales\WysiwygModule\Migration\Factory\MigrationReporterFactory;
use OxidEsales\WysiwygModule\Migration\Factory\MigrationReporterFactoryInterface;
use OxidEsales\WysiwygModule\Migration\Service\MediaMigrationResultFilterInterface;
use PHPUnit\Framework\Attributes\Test;

class MigrationReporterFactoryTest extends IntegrationTestCase
{
    private const REPORT_FILE_NAME = 'media-migration.csv';

    private string $logDirectory;

    public function setUp(): void
    {
        parent::setUp();

        $this->logDirectory = sys_get_temp_dir() . '/wysiwyg-log-' . uniqid();
        mkdir($this->logDirectory);
    }

    public function tearDown(): void
    {
        array_map('unlink', glob($this->logDirectory . '/*') ?: []);
        rmdir($this->logDirectory);

        parent::tearDown();
    }

    #[Test]
    public function relativeReportFileGoesToTheShopLogDirectory(): void
    {
        $sut = $this->getSut(
            context: $this->givenShopLogFileIn($this->logDirectory)
        );

        $reporter = $sut->create(self::REPORT_FILE_NAME);
        $reporter->report($this->createStub(MigrationReportInterface::class));

        $this->assertFileExists($this->logDirectory . '/' . self::REPORT_FILE_NAME);
    }

    #[Test]
    public function absoluteReportFileIsWrittenWhereItWasAskedFor(): void
    {
        $absoluteReportPath = sys_get_temp_dir() . '/wysiwyg-migration-' . uniqid() . '.csv';

        $sut = $this->getSut(
            context: $this->givenShopLogFileIn($this->logDirectory)
        );

        try {
            $reporter = $sut->create($absoluteReportPath);
            $reporter->report($this->createStub(MigrationReportInterface::class));

            $this->assertFileExists($absoluteReportPath);
            $this->assertFileDoesNotExist($this->logDirectory . '/' . basename($absoluteReportPath));
        } finally {
            @unlink($absoluteReportPath);
        }
    }

    private function givenShopLogFileIn(string $directory): ContextInterface
    {
        return $this->createConfiguredStub(
            ContextInterface::class,
            ['getLogFilePath' => $directory . '/oxideshop.log']
        );
    }

    private function getSut(
        ?ContextInterface $context = null,
        ?MediaMigrationResultFilterInterface $resultFilter = null,
    ): MigrationReporterFactoryInterface {
        $context ??= $this->get(ContextInterface::class);
        $resultFilter ??= $this->get(MediaMigrationResultFilterInterface::class);

        return new MigrationReporterFactory(
            context: $context,
            resultFilter: $resultFilter,
        );
    }
}
