<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221019081226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE receipt (id INT AUTO_INCREMENT NOT NULL, finished TINYINT(1) NOT NULL, finished_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipt_item (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, receipt_id INT NOT NULL, cost INT NOT NULL, amount SMALLINT NOT NULL, vat INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_89601E924584665A (product_id), INDEX IDX_89601E922B5CA896 (receipt_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE receipt_item ADD CONSTRAINT FK_89601E924584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE receipt_item ADD CONSTRAINT FK_89601E922B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE receipt_item DROP FOREIGN KEY FK_89601E924584665A');
        $this->addSql('ALTER TABLE receipt_item DROP FOREIGN KEY FK_89601E922B5CA896');
        $this->addSql('DROP TABLE receipt');
        $this->addSql('DROP TABLE receipt_item');
    }
}
