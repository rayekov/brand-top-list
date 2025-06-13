<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250613132441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE admin_users (username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_B4A95E13F85E0677 (username), UNIQUE INDEX UNIQ_B4A95E13D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE brands (brand_name VARCHAR(255) NOT NULL, brand_image VARCHAR(500) NOT NULL, rating INT NOT NULL, id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_7EA24434D17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE countries (iso_code VARCHAR(2) NOT NULL, name VARCHAR(255) NOT NULL, is_default TINYINT(1) DEFAULT 0 NOT NULL, id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_5D66EBAD62B6A45E (iso_code), UNIQUE INDEX UNIQ_5D66EBADD17F50A6 (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE top_list_entries (position INT NOT NULL, is_active TINYINT(1) DEFAULT 1 NOT NULL, id INT AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, brand_id INT NOT NULL, country_id INT NOT NULL, UNIQUE INDEX UNIQ_BFADCB13D17F50A6 (uuid), INDEX IDX_BFADCB1344F5D008 (brand_id), INDEX IDX_BFADCB13F92F3E70 (country_id), UNIQUE INDEX unique_brand_country_position (brand_id, country_id, position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE top_list_entries ADD CONSTRAINT FK_BFADCB1344F5D008 FOREIGN KEY (brand_id) REFERENCES brands (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE top_list_entries ADD CONSTRAINT FK_BFADCB13F92F3E70 FOREIGN KEY (country_id) REFERENCES countries (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE top_list_entries DROP FOREIGN KEY FK_BFADCB1344F5D008
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE top_list_entries DROP FOREIGN KEY FK_BFADCB13F92F3E70
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE admin_users
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE brands
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE countries
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE top_list_entries
        SQL);
    }
}
