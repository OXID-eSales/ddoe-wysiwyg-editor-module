<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Integration\Migration\Repository;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\WysiwygModule\Migration\DTO\ContentMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\MediaMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\Repository\FieldMigrationRepository;
use OxidEsales\WysiwygModule\Migration\Service\MigrationServiceInterface;

class FieldMigrationRepositoryTest extends IntegrationTestCase
{
    private const TABLE = 'oxartextends';
    private const FIELD = 'OXLONGDESC';

    public function testMigrateTableField()
    {
        $queryBuilderFactory = $this->get(QueryBuilderFactoryInterface::class);

        $cleanupTableQueryBuilder = $queryBuilderFactory->create();
        $cleanupTableQueryBuilder->delete(self::TABLE)->execute();

        $originalValue = 'original value ' . uniqid();
        $expectedValue = 'migrated value ' . uniqid();

        $insertQueryBuilder = $queryBuilderFactory->create();
        $insertQueryBuilder->insert(self::TABLE)->values([
            'OXID' => $insertQueryBuilder->createNamedParameter($oxid = uniqid()),
            self::FIELD => $insertQueryBuilder->createNamedParameter($originalValue),
        ])->execute();

        $resultStub = $this->createConfiguredStub(
            ContentMigrationResultInterface::class,
            ['getContent' => $expectedValue]
        );

        $migrationServiceMock = $this->createMock(MigrationServiceInterface::class);
        $migrationServiceMock->method('migrateContent')
            ->with($originalValue, $oxid)
            ->willReturn($resultStub);

        $sut = new FieldMigrationRepository(
            migrationService: $migrationServiceMock,
            queryBuilderFactory: $queryBuilderFactory,
        );
        $sut->migrateTableField(self::TABLE, self::FIELD, 'OXID');

        $selectQueryBuilder = $queryBuilderFactory->create();
        $actualValue = $selectQueryBuilder->select(self::FIELD)->from(self::TABLE)
            ->where('OXID = :oxid')
            ->setParameters([
                ':oxid' => $oxid,
            ])->execute()->fetchOne();

        $this->assertSame($expectedValue, $actualValue);
    }

    public function testMigrateTableFieldReturnsFoundReferencesLocatedInTheirRow()
    {
        $queryBuilderFactory = $this->get(QueryBuilderFactoryInterface::class);

        $cleanupTableQueryBuilder = $queryBuilderFactory->create();
        $cleanupTableQueryBuilder->delete(self::TABLE)->execute();

        $insertQueryBuilder = $queryBuilderFactory->create();
        $insertQueryBuilder->insert(self::TABLE)->values([
            'OXID' => $insertQueryBuilder->createNamedParameter($oxid = uniqid()),
            self::FIELD => $insertQueryBuilder->createNamedParameter('some content'),
        ])->execute();

        $referenceStub = $this->createStub(MediaMigrationResultInterface::class);

        $resultStub = $this->createConfiguredStub(ContentMigrationResultInterface::class, [
            'getContent' => 'migrated content',
            'getReferences' => [$referenceStub],
        ]);

        $migrationServiceMock = $this->createMock(MigrationServiceInterface::class);
        $migrationServiceMock->expects($this->once())
            ->method('migrateContent')
            ->with('some content', $oxid)
            ->willReturn($resultStub);

        $sut = new FieldMigrationRepository(
            migrationService: $migrationServiceMock,
            queryBuilderFactory: $queryBuilderFactory,
        );

        $entries = $sut->migrateTableField(self::TABLE, self::FIELD, 'OXID');

        $this->assertSame([$referenceStub], $entries);
    }
}
