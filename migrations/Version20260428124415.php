<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260428124415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE anime_genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE anime_theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE episode (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, plex_id VARCHAR(255) DEFAULT NULL, tvdb_id INT DEFAULT NULL, season_number INT NOT NULL, episode_number INT NOT NULL, duration INT DEFAULT NULL, is_name_vf TINYINT NOT NULL, serie_id INT NOT NULL, INDEX IDX_DDAA1CDAD94388BD (serie_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE episode_show (id INT AUTO_INCREMENT NOT NULL, show_date DATETIME NOT NULL, user_id INT NOT NULL, episode_id INT NOT NULL, INDEX IDX_56D84521A76ED395 (user_id), INDEX IDX_56D84521362B62A0 (episode_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, rating DOUBLE PRECISION DEFAULT NULL, rating_count INT DEFAULT NULL, aggregated_rating DOUBLE PRECISION DEFAULT NULL, aggregated_rating_count INT DEFAULT NULL, igdb_id INT NOT NULL, game_parent_id INT DEFAULT NULL, INDEX IDX_232B318CA0DC0B72 (game_parent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_player_perspective (game_id INT NOT NULL, player_perspective_id INT NOT NULL, INDEX IDX_6884435DE48FD905 (game_id), INDEX IDX_6884435D210F5D42 (player_perspective_id), PRIMARY KEY (game_id, player_perspective_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_game_mode (game_id INT NOT NULL, game_mode_id INT NOT NULL, INDEX IDX_AE79EA85E48FD905 (game_id), INDEX IDX_AE79EA85E227FA65 (game_mode_id), PRIMARY KEY (game_id, game_mode_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_igdbgenre (game_id INT NOT NULL, igdbgenre_id INT NOT NULL, INDEX IDX_61F649B3E48FD905 (game_id), INDEX IDX_61F649B33AEC1213 (igdbgenre_id), PRIMARY KEY (game_id, igdbgenre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_igdbtheme (game_id INT NOT NULL, igdbtheme_id INT NOT NULL, INDEX IDX_75D39D43E48FD905 (game_id), INDEX IDX_75D39D432178B58B (igdbtheme_id), PRIMARY KEY (game_id, igdbtheme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_game_collection (game_id INT NOT NULL, game_collection_id INT NOT NULL, INDEX IDX_6D216538E48FD905 (game_id), INDEX IDX_6D216538FBADCA96 (game_collection_id), PRIMARY KEY (game_id, game_collection_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_collection (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATE NOT NULL, slug VARCHAR(255) NOT NULL, igdb_id INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_mode (id INT AUTO_INCREMENT NOT NULL, name_eng VARCHAR(255) DEFAULT NULL, name_fra VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_platform (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, release_date DATE DEFAULT NULL, game_company_id INT DEFAULT NULL, INDEX IDX_92162FED7947DD3B (game_company_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_release (id INT AUTO_INCREMENT NOT NULL, release_date DATE NOT NULL, game_platform_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_B857C3221B30B6D (game_platform_id), INDEX IDX_B857C32E48FD905 (game_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_tracker (id INT AUTO_INCREMENT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, complete_date DATE DEFAULT NULL, end_time INT DEFAULT NULL, complete_time INT DEFAULT NULL, is_no_complete TINYINT NOT NULL, rating DOUBLE PRECISION DEFAULT NULL, user_id INT NOT NULL, game_platform_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_39A15580A76ED395 (user_id), INDEX IDX_39A1558021B30B6D (game_platform_id), INDEX IDX_39A15580E48FD905 (game_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE igdbgenre (id INT AUTO_INCREMENT NOT NULL, name_eng VARCHAR(255) DEFAULT NULL, name_fra VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE igdbtheme (id INT AUTO_INCREMENT NOT NULL, name_eng VARCHAR(255) DEFAULT NULL, name_fra VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE involved_game_company (id INT AUTO_INCREMENT NOT NULL, is_developer TINYINT NOT NULL, is_porting TINYINT NOT NULL, is_publisher TINYINT NOT NULL, is_supporting TINYINT NOT NULL, game_id INT NOT NULL, game_company_id INT NOT NULL, INDEX IDX_5BCF169EE48FD905 (game_id), INDEX IDX_5BCF169E7947DD3B (game_company_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE involved_manga_company (id INT AUTO_INCREMENT NOT NULL, is_author TINYINT NOT NULL, is_editor TINYINT NOT NULL, is_designer TINYINT NOT NULL, manga_id INT NOT NULL, manga_company_id INT NOT NULL, INDEX IDX_8B8CB1357B6461 (manga_id), INDEX IDX_8B8CB1353F13670A (manga_company_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE involved_serie_company (id INT AUTO_INCREMENT NOT NULL, is_network TINYINT NOT NULL, is_studio TINYINT NOT NULL, is_producer TINYINT NOT NULL, serie_id INT NOT NULL, company_id INT NOT NULL, INDEX IDX_21A21793D94388BD (serie_id), INDEX IDX_21A21793979B1AD6 (company_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, manga_type_id INT NOT NULL, INDEX IDX_765A9E035BFB5992 (manga_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_manga_genre (manga_id INT NOT NULL, manga_genre_id INT NOT NULL, INDEX IDX_9ACBD91D7B6461 (manga_id), INDEX IDX_9ACBD91D350F545C (manga_genre_id), PRIMARY KEY (manga_id, manga_genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_manga_theme (manga_id INT NOT NULL, manga_theme_id INT NOT NULL, INDEX IDX_8EEE0DED7B6461 (manga_id), INDEX IDX_8EEE0DED2E9BF3C4 (manga_theme_id), PRIMARY KEY (manga_id, manga_theme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_tome (id INT AUTO_INCREMENT NOT NULL, tome_number INT NOT NULL, release_date DATE DEFAULT NULL, page INT DEFAULT NULL, is_last_tome TINYINT NOT NULL, manga_id INT NOT NULL, INDEX IDX_344D0DBD7B6461 (manga_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_tome_read (id INT AUTO_INCREMENT NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, user_id INT NOT NULL, manga_tome_id INT NOT NULL, INDEX IDX_1C27C860A76ED395 (user_id), INDEX IDX_1C27C8601604EB27 (manga_tome_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manga_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mbidtag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, plex_id VARCHAR(255) DEFAULT NULL, mbid_tag_type_id INT DEFAULT NULL, INDEX IDX_EE9F47F38DC65F0 (mbid_tag_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mbidtag_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, tmdb_id VARCHAR(255) DEFAULT NULL, plex_id VARCHAR(255) DEFAULT NULL, duration INT DEFAULT NULL, updated TINYINT NOT NULL, release_date DATE DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_tmdbgenre (movie_id INT NOT NULL, tmdbgenre_id INT NOT NULL, INDEX IDX_ECCA38598F93B6FC (movie_id), INDEX IDX_ECCA38594AF4A685 (tmdbgenre_id), PRIMARY KEY (movie_id, tmdbgenre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movie_show (id INT AUTO_INCREMENT NOT NULL, show_date DATETIME NOT NULL, user_id INT NOT NULL, movie_id INT NOT NULL, INDEX IDX_C168F80CA76ED395 (user_id), INDEX IDX_C168F80C8F93B6FC (movie_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE music (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, duration INT DEFAULT NULL, mbid VARCHAR(255) DEFAULT NULL, plex_id VARCHAR(255) NOT NULL, music_artist_id INT DEFAULT NULL, INDEX IDX_CD52224A655D9A59 (music_artist_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE music_mbidtag (music_id INT NOT NULL, mbidtag_id INT NOT NULL, INDEX IDX_2A6BD588399BBB13 (music_id), INDEX IDX_2A6BD588E4B5F40 (mbidtag_id), PRIMARY KEY (music_id, mbidtag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE music_artist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, mbid VARCHAR(255) DEFAULT NULL, plex_id VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE music_listen (id INT AUTO_INCREMENT NOT NULL, listen_at DATETIME NOT NULL, music_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_57FCA404399BBB13 (music_id), INDEX IDX_57FCA404A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player_perspective (id INT AUTO_INCREMENT NOT NULL, name_eng VARCHAR(255) DEFAULT NULL, name_fra VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, plex_id VARCHAR(255) DEFAULT NULL, tvdb_id INT DEFAULT NULL, is_vf_name TINYINT NOT NULL, slug VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, first_aired DATE DEFAULT NULL, last_aired DATE DEFAULT NULL, next_aired DATE DEFAULT NULL, name_eng VARCHAR(255) DEFAULT NULL, last_season_name VARCHAR(255) DEFAULT NULL, next_aired_format VARCHAR(255) DEFAULT NULL, score INT DEFAULT NULL, serie_type_id INT NOT NULL, INDEX IDX_AA3A9334F1D5FF34 (serie_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie_tvdbtag (serie_id INT NOT NULL, tvdbtag_id INT NOT NULL, INDEX IDX_6DFC36A9D94388BD (serie_id), INDEX IDX_6DFC36A9DC09C6F4 (tvdbtag_id), PRIMARY KEY (serie_id, tvdbtag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie_tvdbgenre (serie_id INT NOT NULL, tvdbgenre_id INT NOT NULL, INDEX IDX_76842213D94388BD (serie_id), INDEX IDX_76842213C723E50A (tvdbgenre_id), PRIMARY KEY (serie_id, tvdbgenre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie_anime_theme (serie_id INT NOT NULL, anime_theme_id INT NOT NULL, INDEX IDX_FCE0B564D94388BD (serie_id), INDEX IDX_FCE0B564BCC2A6AD (anime_theme_id), PRIMARY KEY (serie_id, anime_theme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie_anime_genre (serie_id INT NOT NULL, anime_genre_id INT NOT NULL, INDEX IDX_E8C56194D94388BD (serie_id), INDEX IDX_E8C56194A7560135 (anime_genre_id), PRIMARY KEY (serie_id, anime_genre_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie_company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_7B267AE85E237E06 (name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE serie_update (id INT AUTO_INCREMENT NOT NULL, status_old VARCHAR(255) DEFAULT NULL, status_new VARCHAR(255) DEFAULT NULL, aired_old DATE DEFAULT NULL, aired_new DATE DEFAULT NULL, aired_type_old VARCHAR(255) DEFAULT NULL, aired_type_new VARCHAR(255) DEFAULT NULL, serie_id INT NOT NULL, INDEX IDX_6F8F6E11D94388BD (serie_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tmdbgenre (id INT AUTO_INCREMENT NOT NULL, name_eng VARCHAR(255) DEFAULT NULL, name_fra VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tvdbgenre (id INT AUTO_INCREMENT NOT NULL, name_eng VARCHAR(255) DEFAULT NULL, name_fra VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tvdbtag (id INT AUTO_INCREMENT NOT NULL, name_eng VARCHAR(255) DEFAULT NULL, name_fra VARCHAR(255) DEFAULT NULL, tvdb_tag_type_id INT DEFAULT NULL, INDEX IDX_5965450EEA81001A (tvdb_tag_type_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tvdbtag_type (id INT AUTO_INCREMENT NOT NULL, name_eng VARCHAR(255) DEFAULT NULL, name_fra VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, plex_name VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE episode ADD CONSTRAINT FK_DDAA1CDAD94388BD FOREIGN KEY (serie_id) REFERENCES serie (id)');
        $this->addSql('ALTER TABLE episode_show ADD CONSTRAINT FK_56D84521A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE episode_show ADD CONSTRAINT FK_56D84521362B62A0 FOREIGN KEY (episode_id) REFERENCES episode (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CA0DC0B72 FOREIGN KEY (game_parent_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_player_perspective ADD CONSTRAINT FK_6884435DE48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_player_perspective ADD CONSTRAINT FK_6884435D210F5D42 FOREIGN KEY (player_perspective_id) REFERENCES player_perspective (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_mode ADD CONSTRAINT FK_AE79EA85E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_mode ADD CONSTRAINT FK_AE79EA85E227FA65 FOREIGN KEY (game_mode_id) REFERENCES game_mode (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_igdbgenre ADD CONSTRAINT FK_61F649B3E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_igdbgenre ADD CONSTRAINT FK_61F649B33AEC1213 FOREIGN KEY (igdbgenre_id) REFERENCES igdbgenre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_igdbtheme ADD CONSTRAINT FK_75D39D43E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_igdbtheme ADD CONSTRAINT FK_75D39D432178B58B FOREIGN KEY (igdbtheme_id) REFERENCES igdbtheme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_collection ADD CONSTRAINT FK_6D216538E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_game_collection ADD CONSTRAINT FK_6D216538FBADCA96 FOREIGN KEY (game_collection_id) REFERENCES game_collection (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_platform ADD CONSTRAINT FK_92162FED7947DD3B FOREIGN KEY (game_company_id) REFERENCES game_company (id)');
        $this->addSql('ALTER TABLE game_release ADD CONSTRAINT FK_B857C3221B30B6D FOREIGN KEY (game_platform_id) REFERENCES game_platform (id)');
        $this->addSql('ALTER TABLE game_release ADD CONSTRAINT FK_B857C32E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_tracker ADD CONSTRAINT FK_39A15580A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_tracker ADD CONSTRAINT FK_39A1558021B30B6D FOREIGN KEY (game_platform_id) REFERENCES game_platform (id)');
        $this->addSql('ALTER TABLE game_tracker ADD CONSTRAINT FK_39A15580E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE involved_game_company ADD CONSTRAINT FK_5BCF169EE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE involved_game_company ADD CONSTRAINT FK_5BCF169E7947DD3B FOREIGN KEY (game_company_id) REFERENCES game_company (id)');
        $this->addSql('ALTER TABLE involved_manga_company ADD CONSTRAINT FK_8B8CB1357B6461 FOREIGN KEY (manga_id) REFERENCES manga (id)');
        $this->addSql('ALTER TABLE involved_manga_company ADD CONSTRAINT FK_8B8CB1353F13670A FOREIGN KEY (manga_company_id) REFERENCES manga_company (id)');
        $this->addSql('ALTER TABLE involved_serie_company ADD CONSTRAINT FK_21A21793D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id)');
        $this->addSql('ALTER TABLE involved_serie_company ADD CONSTRAINT FK_21A21793979B1AD6 FOREIGN KEY (company_id) REFERENCES serie_company (id)');
        $this->addSql('ALTER TABLE manga ADD CONSTRAINT FK_765A9E035BFB5992 FOREIGN KEY (manga_type_id) REFERENCES manga_type (id)');
        $this->addSql('ALTER TABLE manga_manga_genre ADD CONSTRAINT FK_9ACBD91D7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manga_manga_genre ADD CONSTRAINT FK_9ACBD91D350F545C FOREIGN KEY (manga_genre_id) REFERENCES manga_genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manga_manga_theme ADD CONSTRAINT FK_8EEE0DED7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manga_manga_theme ADD CONSTRAINT FK_8EEE0DED2E9BF3C4 FOREIGN KEY (manga_theme_id) REFERENCES manga_theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manga_tome ADD CONSTRAINT FK_344D0DBD7B6461 FOREIGN KEY (manga_id) REFERENCES manga (id)');
        $this->addSql('ALTER TABLE manga_tome_read ADD CONSTRAINT FK_1C27C860A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE manga_tome_read ADD CONSTRAINT FK_1C27C8601604EB27 FOREIGN KEY (manga_tome_id) REFERENCES manga_tome (id)');
        $this->addSql('ALTER TABLE mbidtag ADD CONSTRAINT FK_EE9F47F38DC65F0 FOREIGN KEY (mbid_tag_type_id) REFERENCES mbidtag_type (id)');
        $this->addSql('ALTER TABLE movie_tmdbgenre ADD CONSTRAINT FK_ECCA38598F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_tmdbgenre ADD CONSTRAINT FK_ECCA38594AF4A685 FOREIGN KEY (tmdbgenre_id) REFERENCES tmdbgenre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie_show ADD CONSTRAINT FK_C168F80CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE movie_show ADD CONSTRAINT FK_C168F80C8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id)');
        $this->addSql('ALTER TABLE music ADD CONSTRAINT FK_CD52224A655D9A59 FOREIGN KEY (music_artist_id) REFERENCES music_artist (id)');
        $this->addSql('ALTER TABLE music_mbidtag ADD CONSTRAINT FK_2A6BD588399BBB13 FOREIGN KEY (music_id) REFERENCES music (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE music_mbidtag ADD CONSTRAINT FK_2A6BD588E4B5F40 FOREIGN KEY (mbidtag_id) REFERENCES mbidtag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE music_listen ADD CONSTRAINT FK_57FCA404399BBB13 FOREIGN KEY (music_id) REFERENCES music (id)');
        $this->addSql('ALTER TABLE music_listen ADD CONSTRAINT FK_57FCA404A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE serie ADD CONSTRAINT FK_AA3A9334F1D5FF34 FOREIGN KEY (serie_type_id) REFERENCES serie_type (id)');
        $this->addSql('ALTER TABLE serie_tvdbtag ADD CONSTRAINT FK_6DFC36A9D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_tvdbtag ADD CONSTRAINT FK_6DFC36A9DC09C6F4 FOREIGN KEY (tvdbtag_id) REFERENCES tvdbtag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_tvdbgenre ADD CONSTRAINT FK_76842213D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_tvdbgenre ADD CONSTRAINT FK_76842213C723E50A FOREIGN KEY (tvdbgenre_id) REFERENCES tvdbgenre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_anime_theme ADD CONSTRAINT FK_FCE0B564D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_anime_theme ADD CONSTRAINT FK_FCE0B564BCC2A6AD FOREIGN KEY (anime_theme_id) REFERENCES anime_theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_anime_genre ADD CONSTRAINT FK_E8C56194D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_anime_genre ADD CONSTRAINT FK_E8C56194A7560135 FOREIGN KEY (anime_genre_id) REFERENCES anime_genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie_update ADD CONSTRAINT FK_6F8F6E11D94388BD FOREIGN KEY (serie_id) REFERENCES serie (id)');
        $this->addSql('ALTER TABLE tvdbtag ADD CONSTRAINT FK_5965450EEA81001A FOREIGN KEY (tvdb_tag_type_id) REFERENCES tvdbtag_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE episode DROP FOREIGN KEY FK_DDAA1CDAD94388BD');
        $this->addSql('ALTER TABLE episode_show DROP FOREIGN KEY FK_56D84521A76ED395');
        $this->addSql('ALTER TABLE episode_show DROP FOREIGN KEY FK_56D84521362B62A0');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CA0DC0B72');
        $this->addSql('ALTER TABLE game_player_perspective DROP FOREIGN KEY FK_6884435DE48FD905');
        $this->addSql('ALTER TABLE game_player_perspective DROP FOREIGN KEY FK_6884435D210F5D42');
        $this->addSql('ALTER TABLE game_game_mode DROP FOREIGN KEY FK_AE79EA85E48FD905');
        $this->addSql('ALTER TABLE game_game_mode DROP FOREIGN KEY FK_AE79EA85E227FA65');
        $this->addSql('ALTER TABLE game_igdbgenre DROP FOREIGN KEY FK_61F649B3E48FD905');
        $this->addSql('ALTER TABLE game_igdbgenre DROP FOREIGN KEY FK_61F649B33AEC1213');
        $this->addSql('ALTER TABLE game_igdbtheme DROP FOREIGN KEY FK_75D39D43E48FD905');
        $this->addSql('ALTER TABLE game_igdbtheme DROP FOREIGN KEY FK_75D39D432178B58B');
        $this->addSql('ALTER TABLE game_game_collection DROP FOREIGN KEY FK_6D216538E48FD905');
        $this->addSql('ALTER TABLE game_game_collection DROP FOREIGN KEY FK_6D216538FBADCA96');
        $this->addSql('ALTER TABLE game_platform DROP FOREIGN KEY FK_92162FED7947DD3B');
        $this->addSql('ALTER TABLE game_release DROP FOREIGN KEY FK_B857C3221B30B6D');
        $this->addSql('ALTER TABLE game_release DROP FOREIGN KEY FK_B857C32E48FD905');
        $this->addSql('ALTER TABLE game_tracker DROP FOREIGN KEY FK_39A15580A76ED395');
        $this->addSql('ALTER TABLE game_tracker DROP FOREIGN KEY FK_39A1558021B30B6D');
        $this->addSql('ALTER TABLE game_tracker DROP FOREIGN KEY FK_39A15580E48FD905');
        $this->addSql('ALTER TABLE involved_game_company DROP FOREIGN KEY FK_5BCF169EE48FD905');
        $this->addSql('ALTER TABLE involved_game_company DROP FOREIGN KEY FK_5BCF169E7947DD3B');
        $this->addSql('ALTER TABLE involved_manga_company DROP FOREIGN KEY FK_8B8CB1357B6461');
        $this->addSql('ALTER TABLE involved_manga_company DROP FOREIGN KEY FK_8B8CB1353F13670A');
        $this->addSql('ALTER TABLE involved_serie_company DROP FOREIGN KEY FK_21A21793D94388BD');
        $this->addSql('ALTER TABLE involved_serie_company DROP FOREIGN KEY FK_21A21793979B1AD6');
        $this->addSql('ALTER TABLE manga DROP FOREIGN KEY FK_765A9E035BFB5992');
        $this->addSql('ALTER TABLE manga_manga_genre DROP FOREIGN KEY FK_9ACBD91D7B6461');
        $this->addSql('ALTER TABLE manga_manga_genre DROP FOREIGN KEY FK_9ACBD91D350F545C');
        $this->addSql('ALTER TABLE manga_manga_theme DROP FOREIGN KEY FK_8EEE0DED7B6461');
        $this->addSql('ALTER TABLE manga_manga_theme DROP FOREIGN KEY FK_8EEE0DED2E9BF3C4');
        $this->addSql('ALTER TABLE manga_tome DROP FOREIGN KEY FK_344D0DBD7B6461');
        $this->addSql('ALTER TABLE manga_tome_read DROP FOREIGN KEY FK_1C27C860A76ED395');
        $this->addSql('ALTER TABLE manga_tome_read DROP FOREIGN KEY FK_1C27C8601604EB27');
        $this->addSql('ALTER TABLE mbidtag DROP FOREIGN KEY FK_EE9F47F38DC65F0');
        $this->addSql('ALTER TABLE movie_tmdbgenre DROP FOREIGN KEY FK_ECCA38598F93B6FC');
        $this->addSql('ALTER TABLE movie_tmdbgenre DROP FOREIGN KEY FK_ECCA38594AF4A685');
        $this->addSql('ALTER TABLE movie_show DROP FOREIGN KEY FK_C168F80CA76ED395');
        $this->addSql('ALTER TABLE movie_show DROP FOREIGN KEY FK_C168F80C8F93B6FC');
        $this->addSql('ALTER TABLE music DROP FOREIGN KEY FK_CD52224A655D9A59');
        $this->addSql('ALTER TABLE music_mbidtag DROP FOREIGN KEY FK_2A6BD588399BBB13');
        $this->addSql('ALTER TABLE music_mbidtag DROP FOREIGN KEY FK_2A6BD588E4B5F40');
        $this->addSql('ALTER TABLE music_listen DROP FOREIGN KEY FK_57FCA404399BBB13');
        $this->addSql('ALTER TABLE music_listen DROP FOREIGN KEY FK_57FCA404A76ED395');
        $this->addSql('ALTER TABLE serie DROP FOREIGN KEY FK_AA3A9334F1D5FF34');
        $this->addSql('ALTER TABLE serie_tvdbtag DROP FOREIGN KEY FK_6DFC36A9D94388BD');
        $this->addSql('ALTER TABLE serie_tvdbtag DROP FOREIGN KEY FK_6DFC36A9DC09C6F4');
        $this->addSql('ALTER TABLE serie_tvdbgenre DROP FOREIGN KEY FK_76842213D94388BD');
        $this->addSql('ALTER TABLE serie_tvdbgenre DROP FOREIGN KEY FK_76842213C723E50A');
        $this->addSql('ALTER TABLE serie_anime_theme DROP FOREIGN KEY FK_FCE0B564D94388BD');
        $this->addSql('ALTER TABLE serie_anime_theme DROP FOREIGN KEY FK_FCE0B564BCC2A6AD');
        $this->addSql('ALTER TABLE serie_anime_genre DROP FOREIGN KEY FK_E8C56194D94388BD');
        $this->addSql('ALTER TABLE serie_anime_genre DROP FOREIGN KEY FK_E8C56194A7560135');
        $this->addSql('ALTER TABLE serie_update DROP FOREIGN KEY FK_6F8F6E11D94388BD');
        $this->addSql('ALTER TABLE tvdbtag DROP FOREIGN KEY FK_5965450EEA81001A');
        $this->addSql('DROP TABLE anime_genre');
        $this->addSql('DROP TABLE anime_theme');
        $this->addSql('DROP TABLE episode');
        $this->addSql('DROP TABLE episode_show');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_player_perspective');
        $this->addSql('DROP TABLE game_game_mode');
        $this->addSql('DROP TABLE game_igdbgenre');
        $this->addSql('DROP TABLE game_igdbtheme');
        $this->addSql('DROP TABLE game_game_collection');
        $this->addSql('DROP TABLE game_collection');
        $this->addSql('DROP TABLE game_company');
        $this->addSql('DROP TABLE game_mode');
        $this->addSql('DROP TABLE game_platform');
        $this->addSql('DROP TABLE game_release');
        $this->addSql('DROP TABLE game_tracker');
        $this->addSql('DROP TABLE igdbgenre');
        $this->addSql('DROP TABLE igdbtheme');
        $this->addSql('DROP TABLE involved_game_company');
        $this->addSql('DROP TABLE involved_manga_company');
        $this->addSql('DROP TABLE involved_serie_company');
        $this->addSql('DROP TABLE manga');
        $this->addSql('DROP TABLE manga_manga_genre');
        $this->addSql('DROP TABLE manga_manga_theme');
        $this->addSql('DROP TABLE manga_company');
        $this->addSql('DROP TABLE manga_genre');
        $this->addSql('DROP TABLE manga_theme');
        $this->addSql('DROP TABLE manga_tome');
        $this->addSql('DROP TABLE manga_tome_read');
        $this->addSql('DROP TABLE manga_type');
        $this->addSql('DROP TABLE mbidtag');
        $this->addSql('DROP TABLE mbidtag_type');
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE movie_tmdbgenre');
        $this->addSql('DROP TABLE movie_show');
        $this->addSql('DROP TABLE music');
        $this->addSql('DROP TABLE music_mbidtag');
        $this->addSql('DROP TABLE music_artist');
        $this->addSql('DROP TABLE music_listen');
        $this->addSql('DROP TABLE player_perspective');
        $this->addSql('DROP TABLE serie');
        $this->addSql('DROP TABLE serie_tvdbtag');
        $this->addSql('DROP TABLE serie_tvdbgenre');
        $this->addSql('DROP TABLE serie_anime_theme');
        $this->addSql('DROP TABLE serie_anime_genre');
        $this->addSql('DROP TABLE serie_company');
        $this->addSql('DROP TABLE serie_type');
        $this->addSql('DROP TABLE serie_update');
        $this->addSql('DROP TABLE tmdbgenre');
        $this->addSql('DROP TABLE tvdbgenre');
        $this->addSql('DROP TABLE tvdbtag');
        $this->addSql('DROP TABLE tvdbtag_type');
        $this->addSql('DROP TABLE user');
    }
}
