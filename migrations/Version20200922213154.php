<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200922213154 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, last_login_at DATETIME DEFAULT NULL, first_name VARCHAR(128) NOT NULL, last_name VARCHAR(128) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, birth_date DATE NOT NULL, UNIQUE INDEX UNIQ_B723AF33A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notice (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL, text LONGTEXT NOT NULL, INDEX IDX_480D45C2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE module (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(64) NOT NULL, syllabus VARCHAR(16) NOT NULL, min_vote INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE certification (id INT AUTO_INCREMENT NOT NULL, update_certification_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, duration VARCHAR(255) DEFAULT NULL COMMENT \'(DC2Type:dateinterval)\', INDEX IDX_6C3C6D756B9278B (update_certification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skill_card (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, certification_id INT NOT NULL, number VARCHAR(7) NOT NULL, credits INT NOT NULL, expires_at DATETIME DEFAULT NULL, status VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_5155046196901F54 (number), INDEX IDX_51550461CB944F1A (student_id), INDEX IDX_51550461CB47068A (certification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, certification_id INT NOT NULL, datetime DATETIME NOT NULL, subscribe_expire_date DATE NOT NULL, status VARCHAR(32) NOT NULL, type VARCHAR(32) NOT NULL, rounds INT NOT NULL, INDEX IDX_D044D5D4CB47068A (certification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE certification_module (id INT AUTO_INCREMENT NOT NULL, certification_id INT NOT NULL, module_id INT NOT NULL, is_mandatory TINYINT(1) NOT NULL, INDEX IDX_EA0C3F9FCB47068A (certification_id), INDEX IDX_EA0C3F9FAFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE skill_card_module (id INT AUTO_INCREMENT NOT NULL, skill_card_id INT NOT NULL, module_id INT NOT NULL, is_passed TINYINT(1) NOT NULL, INDEX IDX_C005EC9050390409 (skill_card_id), INDEX IDX_C005EC90AFC2B591 (module_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE booking (id INT AUTO_INCREMENT NOT NULL, skill_card_id INT NOT NULL, module_id INT NOT NULL, session_id INT NOT NULL, turn INT NOT NULL, status VARCHAR(32) NOT NULL, is_approved TINYINT(1) NOT NULL, INDEX IDX_E00CEDDE50390409 (skill_card_id), INDEX IDX_E00CEDDEAFC2B591 (module_id), INDEX IDX_E00CEDDE613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D756B9278B FOREIGN KEY (update_certification_id) REFERENCES certification (id)');
        $this->addSql('ALTER TABLE skill_card ADD CONSTRAINT FK_51550461CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE skill_card ADD CONSTRAINT FK_51550461CB47068A FOREIGN KEY (certification_id) REFERENCES certification (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4CB47068A FOREIGN KEY (certification_id) REFERENCES certification (id)');
        $this->addSql('ALTER TABLE certification_module ADD CONSTRAINT FK_EA0C3F9FCB47068A FOREIGN KEY (certification_id) REFERENCES certification (id)');
        $this->addSql('ALTER TABLE certification_module ADD CONSTRAINT FK_EA0C3F9FAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id)');
        $this->addSql('ALTER TABLE skill_card_module ADD CONSTRAINT FK_C005EC9050390409 FOREIGN KEY (skill_card_id) REFERENCES skill_card (id)');
        $this->addSql('ALTER TABLE skill_card_module ADD CONSTRAINT FK_C005EC90AFC2B591 FOREIGN KEY (module_id) REFERENCES certification_module (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE50390409 FOREIGN KEY (skill_card_id) REFERENCES skill_card (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEAFC2B591 FOREIGN KEY (module_id) REFERENCES skill_card_module (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D756B9278B');
        $this->addSql('ALTER TABLE certification_module DROP FOREIGN KEY FK_EA0C3F9FCB47068A');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4CB47068A');
        $this->addSql('ALTER TABLE skill_card DROP FOREIGN KEY FK_51550461CB47068A');
        $this->addSql('ALTER TABLE skill_card_module DROP FOREIGN KEY FK_C005EC90AFC2B591');
        $this->addSql('ALTER TABLE certification_module DROP FOREIGN KEY FK_EA0C3F9FAFC2B591');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE613FECDF');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDE50390409');
        $this->addSql('ALTER TABLE skill_card_module DROP FOREIGN KEY FK_C005EC9050390409');
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEAFC2B591');
        $this->addSql('ALTER TABLE skill_card DROP FOREIGN KEY FK_51550461CB944F1A');
        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C2A76ED395');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33A76ED395');
        $this->addSql('DROP TABLE booking');
        $this->addSql('DROP TABLE certification');
        $this->addSql('DROP TABLE certification_module');
        $this->addSql('DROP TABLE module');
        $this->addSql('DROP TABLE notice');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE skill_card');
        $this->addSql('DROP TABLE skill_card_module');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE user');
    }
}
