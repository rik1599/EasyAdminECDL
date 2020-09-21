<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200921182626 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE skill_card_modules (id INT AUTO_INCREMENT NOT NULL, skill_card_id INT NOT NULL, module_id INT NOT NULL, is_passed TINYINT(1) NOT NULL, INDEX IDX_EBC159A350390409 (skill_card_id), INDEX IDX_EBC159A3AFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE skill_card_modules ADD CONSTRAINT FK_EBC159A350390409 FOREIGN KEY (skill_card_id) REFERENCES skill_card (id)');
        $this->addSql('ALTER TABLE skill_card_modules ADD CONSTRAINT FK_EBC159A3AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('DROP TABLE skill_card_module');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE skill_card_module (skill_card_id INT NOT NULL, module_id INT NOT NULL, INDEX IDX_C005EC9050390409 (skill_card_id), INDEX IDX_C005EC90AFC2B591 (module_id), PRIMARY KEY(skill_card_id, module_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE skill_card_module ADD CONSTRAINT FK_C005EC9050390409 FOREIGN KEY (skill_card_id) REFERENCES skill_card (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skill_card_module ADD CONSTRAINT FK_C005EC90AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('DROP TABLE skill_card_modules');
    }
}
