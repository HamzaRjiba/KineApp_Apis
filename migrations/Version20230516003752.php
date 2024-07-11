<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230516003752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dossiers_medicaux CHANGE patient_id patient_id INT DEFAULT NULL, CHANGE kine_id kine_id INT DEFAULT NULL, CHANGE date_creation date_creation DATE DEFAULT \'CURRENT_TIMESTAMP\' NOT NULL');
        $this->addSql('ALTER TABLE messages DROP role, CHANGE kine_id kine_id INT DEFAULT NULL, CHANGE patient_id patient_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE paiements CHANGE patient_id patient_id INT DEFAULT NULL, CHANGE kine_id kine_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE programme CHANGE dossier_medical_id dossier_medical_id INT DEFAULT NULL, CHANGE exercice_id exercice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rendez_vous CHANGE patient_id patient_id INT DEFAULT NULL, CHANGE kine_id kine_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dossiers_medicaux CHANGE patient_id patient_id INT NOT NULL, CHANGE kine_id kine_id INT NOT NULL, CHANGE date_creation date_creation DATE NOT NULL');
        $this->addSql('ALTER TABLE messages ADD role VARCHAR(255) NOT NULL, CHANGE kine_id kine_id INT NOT NULL, CHANGE patient_id patient_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiements CHANGE kine_id kine_id INT NOT NULL, CHANGE patient_id patient_id INT NOT NULL');
        $this->addSql('ALTER TABLE programme CHANGE dossier_medical_id dossier_medical_id INT NOT NULL, CHANGE exercice_id exercice_id INT NOT NULL');
        $this->addSql('ALTER TABLE rendez_vous CHANGE patient_id patient_id INT NOT NULL, CHANGE kine_id kine_id INT NOT NULL');
    }
}
