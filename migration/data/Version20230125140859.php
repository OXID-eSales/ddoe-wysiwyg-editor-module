<?php

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230125140859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Main module tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE IF NOT EXISTS `ddmedia` (
           `OXID` CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
           `DDFILENAME` VARCHAR(255) NOT NULL,
           `DDFILESIZE` INT(10) UNSIGNED NOT NULL,
           `DDFILETYPE` VARCHAR(50) NOT NULL,
           `DDTHUMB` VARCHAR(255) NOT NULL,
           `OXTIMESTAMP` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
           PRIMARY KEY (`OXID`)
         ) ENGINE=InnoDB;');
    }

    public function down(Schema $schema): void
    {
    }
}
