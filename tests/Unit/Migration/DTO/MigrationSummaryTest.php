<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\DTO;

use OxidEsales\WysiwygModule\Migration\DTO\MigrationSummary;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationSummaryInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MigrationSummaryTest extends TestCase
{
    #[Test]
    public function getLinesReturnsEveryLineInTheGivenOrder(): void
    {
        $lines = [uniqid('line-'), uniqid('line-')];

        $sut = $this->getSut(
            lines: $lines
        );

        $this->assertSame($lines, $sut->getLines());
    }

    #[Test]
    public function summaryHasNothingToSayWhenNoLinesWereGiven(): void
    {
        $sut = $this->getSut(
            lines: []
        );

        $this->assertSame([], $sut->getLines());
    }

    /**
     * @param string[] $lines
     */
    private function getSut(array $lines): MigrationSummaryInterface
    {
        return new MigrationSummary(
            lines: $lines,
        );
    }
}
