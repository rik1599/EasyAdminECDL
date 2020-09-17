<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200917135704 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification ADD update_certification_id INT DEFAULT NULL, ADD duration VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D756B9278B FOREIGN KEY (update_certification_id) REFERENCES certification (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6C3C6D756B9278B ON certification (update_certification_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D756B9278B');
        $this->addSql('DROP INDEX UNIQ_6C3C6D756B9278B ON certification');
        $this->addSql('ALTER TABLE certification DROP update_certification_id, DROP duration');
    }
}
