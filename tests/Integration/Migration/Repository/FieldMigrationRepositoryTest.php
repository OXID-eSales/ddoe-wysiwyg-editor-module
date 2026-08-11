<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Integration\Migration\Repository;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\WysiwygModule\Migration\DTO\ContentMigrationResult;
use OxidEsales\WysiwygModule\Migration\DTO\MediaReferenceResult;
use OxidEsales\WysiwygModule\Migration\DTO\MigrationOutcome;
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

        $migrationServiceMock = $this->createMock(MigrationServiceInterface::class);
        $migrationServiceMock->method('migrateContentWithReferences')
            ->with($originalValue)
            ->willReturn(new ContentMigrationResult($expectedValue, []));

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

        $reference = new MediaReferenceResult(
            attribute: 'src',
            path: '/out/pictures/ddmedia/1.jpg',
            outcome: MigrationOutcome::Converted,
            mediaId: uniqid(),
        );

        $migrationServiceStub = $this->createMock(MigrationServiceInterface::class);
        $migrationServiceStub->method('migrateContentWithReferences')
            ->willReturn(new ContentMigrationResult('migrated content', [$reference]));

        $sut = new FieldMigrationRepository(
            migrationService: $migrationServiceStub,
            queryBuilderFactory: $queryBuilderFactory,
        );

        $entries = $sut->migrateTableField(self::TABLE, self::FIELD, 'OXID');

        $this->assertCount(1, $entries);
        $this->assertSame($oxid, $entries[0]->getKey());
        $this->assertSame($reference, $entries[0]->getReference());
    }
}
