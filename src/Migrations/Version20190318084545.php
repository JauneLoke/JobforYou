<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190318084545 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE emplois_candidats (id INT AUTO_INCREMENT NOT NULL, emplois_id INT NOT NULL, candidat_id INT NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_B713DE371424CD53 (emplois_id), INDEX IDX_B713DE378D0EB82 (candidat_id), UNIQUE INDEX emploi_candidat (emplois_id, candidat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE emplois_candidats ADD CONSTRAINT FK_B713DE371424CD53 FOREIGN KEY (emplois_id) REFERENCES emplois (id)');
        $this->addSql('ALTER TABLE emplois_candidats ADD CONSTRAINT FK_B713DE378D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('DROP TABLE candidature');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE candidature (id INT AUTO_INCREMENT NOT NULL, emplois_id INT NOT NULL, candidat_id INT NOT NULL, description LONGTEXT DEFAULT NULL COLLATE utf8mb4_unicode_ci, INDEX IDX_E33BD3B88D0EB82 (candidat_id), INDEX IDX_E33BD3B81424CD53 (emplois_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B81424CD53 FOREIGN KEY (emplois_id) REFERENCES emplois (id)');
        $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B88D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id)');
        $this->addSql('DROP TABLE emplois_candidats');
    }
}
