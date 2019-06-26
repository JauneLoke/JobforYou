<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190301211019 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE emplois_unique_views (id INT AUTO_INCREMENT NOT NULL, emplois_id INT NOT NULL, adresse_ip VARCHAR(50) NOT NULL, date_visited DATETIME NOT NULL, INDEX IDX_202F48881424CD53 (emplois_id), INDEX adresseIp (adresse_ip), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE emplois_unique_views ADD CONSTRAINT FK_202F48881424CD53 FOREIGN KEY (emplois_id) REFERENCES emplois (id)');
        $this->addSql('ALTER TABLE emplois DROP unique_view');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE emplois_unique_views');
        $this->addSql('ALTER TABLE emplois ADD unique_view INT NOT NULL');
    }
}
