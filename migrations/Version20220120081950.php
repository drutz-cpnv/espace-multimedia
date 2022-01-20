<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220120081950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE teacher (id INT AUTO_INCREMENT NOT NULL, user_teacher_id INT DEFAULT NULL, fullname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, acronym VARCHAR(3) NOT NULL, friendly_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_B0F6A6D5E6E7B8F1 (user_teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE teacher ADD CONSTRAINT FK_B0F6A6D5E6E7B8F1 FOREIGN KEY (user_teacher_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD teacher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939841807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('CREATE INDEX IDX_F529939841807E1D ON `order` (teacher_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939841807E1D');
        $this->addSql('DROP TABLE teacher');
        $this->addSql('DROP INDEX IDX_F529939841807E1D ON `order`');
        $this->addSql('ALTER TABLE `order` DROP teacher_id');
    }
}
