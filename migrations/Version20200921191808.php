<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200921191808 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP INDEX UNIQ_6C3C6D756B9278B, ADD INDEX IDX_6C3C6D756B9278B (update_certification_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP INDEX IDX_6C3C6D756B9278B, ADD UNIQUE INDEX UNIQ_6C3C6D756B9278B (update_certification_id)');
    }
}
