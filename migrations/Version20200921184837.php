<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200921184837 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEAFC2B591');
        $this->addSql('ALTER TABLE booking ADD status VARCHAR(32) NOT NULL, CHANGE round turn INT NOT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEAFC2B591 FOREIGN KEY (module_id) REFERENCES skill_card_modules (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEAFC2B591');
        $this->addSql('ALTER TABLE booking DROP status, CHANGE turn round INT NOT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
