<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260508130732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anime_theme DROP is_spoiler');
        $this->addSql('ALTER TABLE serie_anime_theme DROP FOREIGN KEY `FK_FCE0B564BCC2A6AD`');
        $this->addSql('ALTER TABLE serie_anime_theme DROP FOREIGN KEY `FK_FCE0B564D94388BD`');
        $this->addSql('ALTER TABLE serie_anime_theme ADD id INT AUTO_INCREMENT NOT NULL, ADD is_spoiler TINYINT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE serie_anime_theme ADD CONSTRAINT FK_FCE0B564BCC2A6AD FOREIGN KEY (anime_theme_id) REFERENCES anime_theme (id)');
        $this->addSql('ALTER TABLE serie_anime_theme ADD CONSTRAINT FK_FCE0B564D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anime_theme ADD is_spoiler TINYINT NOT NULL');
        $this->addSql('ALTER TABLE serie_anime_theme DROP FOREIGN KEY FK_FCE0B564D94388BD');
        $this->addSql('ALTER TABLE serie_anime_theme DROP FOREIGN KEY FK_FCE0B564BCC2A6AD');
        $this->addSql('ALTER TABLE serie_anime_theme MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE serie_anime_theme DROP id, DROP is_spoiler, DROP PRIMARY KEY, ADD PRIMARY KEY (serie_id, anime_theme_id)');
        $this->addSql('ALTER TABLE serie_anime_theme ADD CONSTRAINT `FK_FCE0B564D94388BD` FOREIGN KEY (serie_id) REFERENCES serie (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_anime_theme ADD CONSTRAINT `FK_FCE0B564BCC2A6AD` FOREIGN KEY (anime_theme_id) REFERENCES anime_theme (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
