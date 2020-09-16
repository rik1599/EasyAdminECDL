<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200916163843 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE skill_card_module MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE skill_card_module DROP FOREIGN KEY FK_C005EC9050390409');
        $this->addSql('ALTER TABLE skill_card_module DROP FOREIGN KEY FK_C005EC90AFC2B591');
        $this->addSql('ALTER TABLE skill_card_module DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE skill_card_module DROP id, DROP is_passed');
        $this->addSql('ALTER TABLE skill_card_module ADD CONSTRAINT FK_C005EC9050390409 FOREIGN KEY (skill_card_id) REFERENCES skill_card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skill_card_module ADD CONSTRAINT FK_C005EC90AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skill_card_module ADD PRIMARY KEY (skill_card_id, module_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE skill_card_module DROP FOREIGN KEY FK_C005EC9050390409');
        $this->addSql('ALTER TABLE skill_card_module DROP FOREIGN KEY FK_C005EC90AFC2B591');
        $this->addSql('ALTER TABLE skill_card_module ADD id INT AUTO_INCREMENT NOT NULL, ADD is_passed TINYINT(1) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE skill_card_module ADD CONSTRAINT FK_C005EC9050390409 FOREIGN KEY (skill_card_id) REFERENCES skill_card (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE skill_card_module ADD CONSTRAINT FK_C005EC90AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
