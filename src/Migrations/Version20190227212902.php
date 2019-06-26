<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190227212902 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE emplois ADD type_contrat_id INT NOT NULL, ADD type_experience_id INT NOT NULL');
        $this->addSql('ALTER TABLE emplois ADD CONSTRAINT FK_461274B9520D03A FOREIGN KEY (type_contrat_id) REFERENCES type_contrat (id)');
        $this->addSql('ALTER TABLE emplois ADD CONSTRAINT FK_461274B93604B912 FOREIGN KEY (type_experience_id) REFERENCES type_experience (id)');
        $this->addSql('CREATE INDEX IDX_461274B9520D03A ON emplois (type_contrat_id)');
        $this->addSql('CREATE INDEX IDX_461274B93604B912 ON emplois (type_experience_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE emplois DROP FOREIGN KEY FK_461274B9520D03A');
        $this->addSql('ALTER TABLE emplois DROP FOREIGN KEY FK_461274B93604B912');
        $this->addSql('DROP INDEX IDX_461274B9520D03A ON emplois');
        $this->addSql('DROP INDEX IDX_461274B93604B912 ON emplois');
        $this->addSql('ALTER TABLE emplois DROP type_contrat_id, DROP type_experience_id');
    }
}
