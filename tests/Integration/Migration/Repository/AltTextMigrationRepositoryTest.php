<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Tests\Integration\Migration\Repository;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use OxidEsales\WysiwygModule\Migration\DTO\AltTextMigrationResultInterface;
use OxidEsales\WysiwygModule\Migration\DTO\CustomAltTextTagInterface;
use OxidEsales\WysiwygModule\Migration\DTO\TableFieldInterface;
use OxidEsales\WysiwygModule\Migration\Repository\AltTextMigrationRepository;
use OxidEsales\WysiwygModule\Migration\Repository\AltTextMigrationRepositoryInterface;
use OxidEsales\WysiwygModule\Migration\Service\AltTextMigrationServiceInterface;
use PHPUnit\Framework\Attributes\Test;

class AltTextMigrationRepositoryTest extends IntegrationTestCase
{
    private const TABLE = 'oxartextends';
    private const FIELD = 'OXLONGDESC';
    private const KEY_FIELD = 'OXID';

    #[Test]
    public function migrateTableField(): void
    {
        $oxid = $this->insertRow($originalValue = 'original value ' . uniqid());

        $serviceMock = $this->createMock(AltTextMigrationServiceInterface::class);
        $serviceMock->method('migrateAltTexts')
            ->with($originalValue, $oxid)
            ->willReturn($this->createConfiguredStub(AltTextMigrationResultInterface::class, [
                'getContent' => $expectedValue = 'migrated value ' . uniqid(),
            ]));

        $sut = $this->getSut(altTextMigrationService: $serviceMock);
        $sut->migrateTableField($this->createTableFieldStub());

        $this->assertSame($expectedValue, $this->fetchFieldValue($oxid));
    }

    #[Test]
    public function migrateTableFieldReturnsCustomAltTextTagsOfTheService(): void
    {
        $this->insertRow($content = 'content ' . uniqid());

        $customAltTextTagStub = $this->createStub(CustomAltTextTagInterface::class);

        $serviceStub = $this->createConfiguredStub(AltTextMigrationServiceInterface::class, [
            'migrateAltTexts' => $this->createConfiguredStub(AltTextMigrationResultInterface::class, [
                'getContent' => $content,
                'getCustomAltTextTags' => [$customAltTextTagStub],
            ]),
        ]);

        $sut = $this->getSut(altTextMigrationService: $serviceStub);
        $customAltTextTags = $sut->migrateTableField($this->createTableFieldStub());

        $this->assertSame([$customAltTextTagStub], $customAltTextTags);
    }

    private function insertRow(string $content): string
    {
        $queryBuilderFactory = $this->get(QueryBuilderFactoryInterface::class);
        $queryBuilderFactory->create()->delete(self::TABLE)->execute();

        $insertQueryBuilder = $queryBuilderFactory->create();
        $insertQueryBuilder->insert(self::TABLE)->values([
            self::KEY_FIELD => $insertQueryBuilder->createNamedParameter($oxid = uniqid()),
            self::FIELD => $insertQueryBuilder->createNamedParameter($content),
        ])->execute();

        return $oxid;
    }

    private function fetchFieldValue(string $oxid): string
    {
        return (string)$this->get(QueryBuilderFactoryInterface::class)->create()
            ->select(self::FIELD)
            ->from(self::TABLE)
            ->where(self::KEY_FIELD . ' = :oxid')
            ->setParameter('oxid', $oxid)
            ->execute()
            ->fetchOne();
    }

    private function createTableFieldStub(): TableFieldInterface
    {
        return $this->createConfiguredStub(TableFieldInterface::class, [
            'getTable' => self::TABLE,
            'getField' => self::FIELD,
            'getKeyField' => self::KEY_FIELD,
        ]);
    }

    private function getSut(
        ?AltTextMigrationServiceInterface $altTextMigrationService = null,
    ): AltTextMigrationRepositoryInterface {
        $altTextMigrationService ??= $this->createStub(AltTextMigrationServiceInterface::class);

        return new AltTextMigrationRepository(
            altTextMigrationService: $altTextMigrationService,
            queryBuilderFactory: $this->get(QueryBuilderFactoryInterface::class),
        );
    }
}
