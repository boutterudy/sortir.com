<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220124134907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE outlet_user DROP FOREIGN KEY FK_CD69C739CAFD2DE2');
        $this->addSql('CREATE TABLE outing (id INT AUTO_INCREMENT NOT NULL, organizer_id INT NOT NULL, campus_id INT NOT NULL, status_id INT NOT NULL, place_id INT NOT NULL, name VARCHAR(255) NOT NULL, start_at DATETIME NOT NULL, duration VARCHAR(255) NOT NULL COMMENT \'(DC2Type:dateinterval)\', limit_subscription_date DATETIME NOT NULL, about LONGTEXT NOT NULL, max_users INT NOT NULL, INDEX IDX_F2A10625876C4DDA (organizer_id), INDEX IDX_F2A10625AF5D55E1 (campus_id), INDEX IDX_F2A106256BF700BD (status_id), INDEX IDX_F2A10625DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE outing_user (outing_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_2CCED92AF4C7531 (outing_id), INDEX IDX_2CCED92A76ED395 (user_id), PRIMARY KEY(outing_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A10625876C4DDA FOREIGN KEY (organizer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A10625AF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A106256BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A10625DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE outing_user ADD CONSTRAINT FK_2CCED92AF4C7531 FOREIGN KEY (outing_id) REFERENCES outing (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outing_user ADD CONSTRAINT FK_2CCED92A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE outlet');
        $this->addSql('DROP TABLE outlet_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE outing_user DROP FOREIGN KEY FK_2CCED92AF4C7531');
        $this->addSql('CREATE TABLE outlet (id INT AUTO_INCREMENT NOT NULL, organizer_id INT NOT NULL, campus_id INT NOT NULL, status_id INT NOT NULL, place_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, start_at DATETIME NOT NULL, duration VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:dateinterval)\', limit_subscription_date DATETIME NOT NULL, about LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, max_users INT NOT NULL, INDEX IDX_93205CDBAF5D55E1 (campus_id), INDEX IDX_93205CDB6BF700BD (status_id), INDEX IDX_93205CDBDA6A219 (place_id), INDEX IDX_93205CDB876C4DDA (organizer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE outlet_user (outlet_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_CD69C739CAFD2DE2 (outlet_id), INDEX IDX_CD69C739A76ED395 (user_id), PRIMARY KEY(outlet_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE outlet ADD CONSTRAINT FK_93205CDB876C4DDA FOREIGN KEY (organizer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE outlet ADD CONSTRAINT FK_93205CDBDA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE outlet ADD CONSTRAINT FK_93205CDB6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE outlet ADD CONSTRAINT FK_93205CDBAF5D55E1 FOREIGN KEY (campus_id) REFERENCES campus (id)');
        $this->addSql('ALTER TABLE outlet_user ADD CONSTRAINT FK_CD69C739CAFD2DE2 FOREIGN KEY (outlet_id) REFERENCES outlet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE outlet_user ADD CONSTRAINT FK_CD69C739A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE outing');
        $this->addSql('DROP TABLE outing_user');
    }
}
