<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Unit\Migration\DTO;

use OxidEsales\WysiwygModule\Migration\DTO\TableField;
use OxidEsales\WysiwygModule\Migration\DTO\TableFieldInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TableFieldTest extends TestCase
{
    #[Test]
    public function tableFieldExposesGivenValues(): void
    {
        $sut = $this->getSut(
            table: $table = uniqid('table'),
            field: $field = uniqid('field'),
            keyField: $keyField = uniqid('keyField'),
        );

        $this->assertSame($table, $sut->getTable());
        $this->assertSame($field, $sut->getField());
        $this->assertSame($keyField, $sut->getKeyField());
    }

    private function getSut(string $table, string $field, string $keyField): TableFieldInterface
    {
        return new TableField(
            table: $table,
            field: $field,
            keyField: $keyField,
        );
    }
}
