<?php

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230125141525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update module tables';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');

        $mediaTable = $schema->getTable('ddmedia');

        if (!$mediaTable->hasColumn('DDIMAGESIZE')) {
            $this->addSql('ALTER TABLE  `ddmedia` ADD  `DDIMAGESIZE` VARCHAR( 100 ) AFTER  `DDTHUMB`;');
        }

        if (!$mediaTable->hasColumn('OXSHOPID')) {
            $this->addSql('ALTER TABLE  `ddmedia` ADD `OXSHOPID` INT(10) UNSIGNED NOT NULL AFTER `OXID`;');
        }

        if (!$mediaTable->hasColumn('DDFOLDERID')) {
            $this->addSql(
                "ALTER TABLE  `ddmedia` ADD  `DDFOLDERID` CHAR( 32 ) NOT NULL DEFAULT '' AFTER `DDIMAGESIZE`"
            );
        }
    }

    public function down(Schema $schema): void
    {
    }
}
