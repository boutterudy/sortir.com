<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220126093028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campus (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outing (id INT AUTO_INCREMENT NOT NULL, organizer_id INT NOT NULL, campus_id INT NOT NULL, status_id INT NOT NULL, place_id INT NOT NULL, name VARCHAR(255) NOT NULL, start_at DATETIME NOT NULL, duration INT NOT NULL, limit_subscription_date DATETIME NOT NULL, about LONGTEXT NOT NULL, max_users INT NOT NULL, INDEX IDX_F2A10625876C4DDA (organizer_id), INDEX IDX_F2A10625AF5D55E1 (campus_id), INDEX IDX_F2A106256BF700BD (status_id), INDEX IDX_F2A10625DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outing_user (outing_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2CCED92AF4C7531 (outing_id), INDEX IDX_2CCED92A76ED395 (user_id), PRIMARY KEY(outing_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, town_id INT NOT NULL, name VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, INDEX IDX_741D53CD75E23604 (town_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE town (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, campus_id INT NOT NULL, nick_name VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_admin TINYINT(1) NOT NULL, is_active TINYINT(1) NOT NULL, image_file VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649A045A5E9 (nick_name), INDEX IDX_8D93D649AF5D55E1 (campus_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A10625876C4DDA FOREIGN KEY (organizer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A10625AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A106256BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A10625DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE outing_user ADD CONSTRAINT FK_2CCED92AF4C7531 FOREIGN KEY (outing_id) REFERENCES outing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outing_user ADD CONSTRAINT FK_2CCED92A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD75E23604 FOREIGN KEY (town_id) REFERENCES town (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A10625AF5D55E1');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AF5D55E1');
        $this->addSql('ALTER TABLE outing_user DROP FOREIGN KEY FK_2CCED92AF4C7531');
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A10625DA6A219');
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A106256BF700BD');
        $this->addSql('ALTER TABLE place DROP FOREIGN KEY FK_741D53CD75E23604');
        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A10625876C4DDA');
        $this->addSql('ALTER TABLE outing_user DROP FOREIGN KEY FK_2CCED92A76ED395');
        $this->addSql('DROP TABLE campus');
        $this->addSql('DROP TABLE outing');
        $this->addSql('DROP TABLE outing_user');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE town');
        $this->addSql('DROP TABLE user');
    }
}