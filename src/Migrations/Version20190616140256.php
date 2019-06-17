<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190616140256 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE domaine (id INT AUTO_INCREMENT NOT NULL, categorie VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projet (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, titre VARCHAR(255) NOT NULL, date_debut_inscription DATE NOT NULL, date_fin_inscription DATE NOT NULL, description LONGTEXT NOT NULL, website VARCHAR(255) NOT NULL, date_debut_evenement DATE NOT NULL, date_fin_evenement DATE NOT NULL, image VARCHAR(255) NOT NULL, ville VARCHAR(80) NOT NULL, pays VARCHAR(80) NOT NULL, budget INT NOT NULL, frais INT NOT NULL, document VARCHAR(255) DEFAULT NULL, INDEX IDX_50159CA9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projet_domaine (projet_id INT NOT NULL, domaine_id INT NOT NULL, INDEX IDX_FA8557BDC18272 (projet_id), INDEX IDX_FA8557BD4272FC9F (domaine_id), PRIMARY KEY(projet_id, domaine_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(80) DEFAULT NULL, nom VARCHAR(80) NOT NULL, email LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', password VARCHAR(255) NOT NULL, role LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE projet ADD CONSTRAINT FK_50159CA9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE projet_domaine ADD CONSTRAINT FK_FA8557BDC18272 FOREIGN KEY (projet_id) REFERENCES projet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projet_domaine ADD CONSTRAINT FK_FA8557BD4272FC9F FOREIGN KEY (domaine_id) REFERENCES domaine (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE projet_domaine DROP FOREIGN KEY FK_FA8557BD4272FC9F');
        $this->addSql('ALTER TABLE projet_domaine DROP FOREIGN KEY FK_FA8557BDC18272');
        $this->addSql('ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9A76ED395');
        $this->addSql('DROP TABLE domaine');
        $this->addSql('DROP TABLE projet');
        $this->addSql('DROP TABLE projet_domaine');
        $this->addSql('DROP TABLE user');
    }
}
