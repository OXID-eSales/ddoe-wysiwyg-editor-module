<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\DTO;

use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReport;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationReportInterface;
use OxidEsales\WysiwygModule\Migration\DTO\TableFieldInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MigrationReportTest extends TestCase
{
    #[Test]
    public function reportExposesWhatTheRunWasAbout(): void
    {
        $tableFieldStub = $this->createStub(TableFieldInterface::class);

        $sut = $this->getSut(tableField: $tableFieldStub);

        $this->assertSame($tableFieldStub, $sut->getTableField());
        $this->assertSame([], $sut->getEntries());
    }

    #[Test]
    public function getEntriesReturnsEveryEntryInTheGivenOrder(): void
    {
        $entries = [
            $this->createStub(MediaMigrationResultInterface::class),
            $this->createStub(MediaMigrationResultInterface::class),
        ];

        $sut = $this->getSut(entries: $entries);

        $this->assertSame($entries, $sut->getEntries());
    }

    /**
     * @param MediaMigrationResultInterface[] $entries
     */
    private function getSut(
        ?TableFieldInterface $tableField = null,
        array $entries = [],
    ): MigrationReportInterface {
        $tableField ??= $this->createStub(TableFieldInterface::class);

        return new MigrationReport(
            tableField: $tableField,
            entries: $entries,
        );
    }
}
