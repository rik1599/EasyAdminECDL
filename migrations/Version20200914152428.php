<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200914152428 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certification_module (id INT AUTO_INCREMENT NOT NULL, certification_id INT NOT NULL, module_id INT NOT NULL, is_mandatory TINYINT(1) NOT NULL, INDEX IDX_EA0C3F9FCB47068A (certification_id), INDEX IDX_EA0C3F9FAFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE certification_module ADD CONSTRAINT FK_EA0C3F9FCB47068A FOREIGN KEY (certification_id) REFERENCES certification (id)');
        $this->addSql('ALTER TABLE certification_module ADD CONSTRAINT FK_EA0C3F9FAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE certification ADD expiry_time_interval VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE certification_module');
        $this->addSql('ALTER TABLE certification DROP expiry_time_interval');
    }
}
