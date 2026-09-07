<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\Service;

use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
use OxidEsales\WysiwygModule\Migration\Service\MediaMigrationResultFilter;
use OxidEsales\WysiwygModule\Migration\Service\MediaMigrationResultFilterInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MediaMigrationResultFilterTest extends TestCase
{
    #[Test]
    public function filterByOutcomeReturnsOnlyMatchingResultsReindexed(): void
    {
        $failedResult = $this->createConfiguredStub(
            MediaMigrationResultInterface::class,
            ['getOutcome' => MigrationOutcome::Failed]
        );
        $convertedResult = $this->createConfiguredStub(
            MediaMigrationResultInterface::class,
            ['getOutcome' => MigrationOutcome::Converted]
        );

        $sut = $this->getSut();

        $failures = $sut->filterByOutcome(
            [$convertedResult, $failedResult, $convertedResult],
            MigrationOutcome::Failed
        );

        $this->assertSame([$failedResult], $failures);
    }

    #[Test]
    public function filterByOutcomeIsEmptyWhenNoResultMatches(): void
    {
        $convertedResult = $this->createConfiguredStub(
            MediaMigrationResultInterface::class,
            ['getOutcome' => MigrationOutcome::Converted]
        );

        $sut = $this->getSut();

        $this->assertSame([], $sut->filterByOutcome([$convertedResult], MigrationOutcome::Failed));
    }

    #[Test]
    public function filterByOutcomeIsEmptyWhenThereIsNothingToFilter(): void
    {
        $sut = $this->getSut();

        $this->assertSame([], $sut->filterByOutcome([], MigrationOutcome::Converted));
    }

    private function getSut(): MediaMigrationResultFilterInterface
    {
        return new MediaMigrationResultFilter();
    }
}
