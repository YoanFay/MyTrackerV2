<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260606115409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_release ADD status_id INT NOT NULL');
        $this->addSql('ALTER TABLE game_release ADD CONSTRAINT FK_B857C326BF700BD FOREIGN KEY (status_id) REFERENCES game_release_status (id)');
        $this->addSql('CREATE INDEX IDX_B857C326BF700BD ON game_release (status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_release DROP FOREIGN KEY FK_B857C326BF700BD');
        $this->addSql('DROP INDEX IDX_B857C326BF700BD ON game_release');
        $this->addSql('ALTER TABLE game_release DROP status_id');
    }
}
