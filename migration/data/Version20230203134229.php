<?php

declare(strict_types=1);

namespace OxidEsales\WysiwygModule\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230203134229 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update module tables';
    }

    public function up(Schema $schema): void
    {
        $mediaTable = $schema->getTable('ddmedia');

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
