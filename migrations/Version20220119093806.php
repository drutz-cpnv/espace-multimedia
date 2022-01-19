<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220119093806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_state ADD order_id INT NOT NULL, DROP item_order');
        $this->addSql('ALTER TABLE order_state ADD CONSTRAINT FK_200DA6068D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_200DA6068D9F6D38 ON order_state (order_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_state DROP FOREIGN KEY FK_200DA6068D9F6D38');
        $this->addSql('DROP INDEX IDX_200DA6068D9F6D38 ON order_state');
        $this->addSql('ALTER TABLE order_state ADD item_order VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP order_id');
    }
}
