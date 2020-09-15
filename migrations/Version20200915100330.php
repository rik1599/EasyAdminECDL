<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200915100330 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE skill_card (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, certification_id INT NOT NULL, number VARCHAR(7) NOT NULL, credits INT NOT NULL, INDEX IDX_51550461CB944F1A (student_id), INDEX IDX_51550461CB47068A (certification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE skill_card ADD CONSTRAINT FK_51550461CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE skill_card ADD CONSTRAINT FK_51550461CB47068A FOREIGN KEY (certification_id) REFERENCES certification (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE skill_card');
    }
}
