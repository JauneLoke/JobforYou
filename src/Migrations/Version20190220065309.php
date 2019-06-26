<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190220065309 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE emplois (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT NOT NULL, intitule VARCHAR(255) NOT NULL, type_emploi INT NOT NULL, salaire_brut DOUBLE PRECISION NOT NULL, ville VARCHAR(100) NOT NULL, qualification INT NOT NULL, description LONGTEXT NOT NULL, responsabilite LONGTEXT DEFAULT NULL, etude LONGTEXT DEFAULT NULL, experience LONGTEXT DEFAULT NULL, avantage LONGTEXT DEFAULT NULL, active TINYINT(1) NOT NULL, date_add DATETIME NOT NULL, date_upd DATETIME NOT NULL, INDEX IDX_461274B9A4AEAFEA (entreprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE emplois ADD CONSTRAINT FK_461274B9A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE entreprise DROP photos');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE emplois');
        $this->addSql('ALTER TABLE entreprise ADD photos LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
