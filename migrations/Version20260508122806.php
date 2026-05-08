<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260508122806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anime_genre ADD name_fra VARCHAR(255) DEFAULT NULL, CHANGE name name_eng VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE anime_theme ADD name_fra VARCHAR(255) DEFAULT NULL, ADD description_eng LONGTEXT DEFAULT NULL, ADD description_fra LONGTEXT DEFAULT NULL, ADD level INT NOT NULL, ADD is_spoiler TINYINT NOT NULL, CHANGE name name_eng VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE anime_genre DROP name_fra, CHANGE name_eng name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE anime_theme DROP name_fra, DROP description_eng, DROP description_fra, DROP level, DROP is_spoiler, CHANGE name_eng name VARCHAR(255) NOT NULL');
    }
}
