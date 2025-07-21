<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Integration\Migration\Repository;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\WysiwygModule\Migration\Repository\FieldMigrationRepository;
use OxidEsales\WysiwygModule\Migration\Service\MigrationServiceInterface;

class FieldMigrationRepositoryTest extends IntegrationTestCase
{
    public function testMigrateTableField()
    {
        $queryBuilderFactory = $this->get(QueryBuilderFactoryInterface::class);

        $table = 'oxartextends';
        $field = 'OXLONGDESC';

        $cleanupTableQueryBuilder = $queryBuilderFactory->create();
        $cleanupTableQueryBuilder->delete($table)->execute();

        $originalValue = 'original value ' . uniqid();
        $expectedValue = 'migrated value ' . uniqid();

        $insertQueryBuilder = $queryBuilderFactory->create();
        $insertQueryBuilder->insert($table)->values([
            'OXID' => $insertQueryBuilder->createNamedParameter($oxid = uniqid()),
            $field => $insertQueryBuilder->createNamedParameter($originalValue),
        ])->execute();

        $migrationServiceMock = $this->createMock(MigrationServiceInterface::class);
        $migrationServiceMock->method('migrateContent')
            ->with($originalValue)
            ->willReturn($expectedValue);

        $sut = new FieldMigrationRepository(
            migrationService: $migrationServiceMock,
            queryBuilderFactory: $queryBuilderFactory
        );
        $sut->migrateTableField($table, $field, 'OXID');

        $selectQueryBuilder = $queryBuilderFactory->create();
        $actualValue = $selectQueryBuilder->select($field)->from($table)
            ->where('OXID = :oxid')
            ->setParameters([
                ':oxid' => $oxid,
            ])->execute()->fetchOne();

        $this->assertSame($expectedValue, $actualValue);
    }
}
