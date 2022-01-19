<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119094050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_state ADD created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE order_state ADD CONSTRAINT FK_200DA606B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_200DA606B03A8386 ON order_state (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_state DROP FOREIGN KEY FK_200DA606B03A8386');
        $this->addSql('DROP INDEX IDX_200DA606B03A8386 ON order_state');
        $this->addSql('ALTER TABLE order_state DROP created_by_id');
    }
}
