<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190207214832 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, denomination VARCHAR(128) NOT NULL, nom_marque VARCHAR(128) DEFAULT NULL, date_add DATETIME NOT NULL, date_upd DATETIME NOT NULL, adresse VARCHAR(255) NOT NULL, code_postal VARCHAR(10) NOT NULL, ville VARCHAR(200) NOT NULL, telephone VARCHAR(15) DEFAULT NULL, site_web VARCHAR(255) DEFAULT NULL, siren VARCHAR(30) NOT NULL, siret VARCHAR(30) NOT NULL, naf_ape VARCHAR(10) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, surface INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidat (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, nom VARCHAR(128) NOT NULL, prenom VARCHAR(128) NOT NULL, genre INT NOT NULL, naissance DATETIME NOT NULL, adresse VARCHAR(255) NOT NULL, code_postal VARCHAR(10) NOT NULL, ville VARCHAR(200) NOT NULL, telephone VARCHAR(15) DEFAULT NULL, site_web VARCHAR(255) DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, education LONGTEXT DEFAULT NULL, experience LONGTEXT DEFAULT NULL, competence_description VARCHAR(255) DEFAULT NULL, competence LONGTEXT DEFAULT NULL, bonus LONGTEXT DEFAULT NULL, cv VARCHAR(255) DEFAULT NULL, date_add DATETIME NOT NULL, date_upd DATETIME NOT NULL, UNIQUE INDEX UNIQ_6AB5B471A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidat ADD CONSTRAINT FK_6AB5B471A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE candidat');
    }
}
