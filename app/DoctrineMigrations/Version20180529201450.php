<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180529201450 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories_products (category_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_6544F36312469DE2 (category_id), INDEX IDX_6544F3634584665A (product_id), PRIMARY KEY(category_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, code VARCHAR(60) NOT NULL, price DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_D34A04AD5E237E06 (name), UNIQUE INDEX UNIQ_D34A04AD77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categories_products ADD CONSTRAINT FK_6544F36312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE categories_products ADD CONSTRAINT FK_6544F3634584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE categories_products DROP FOREIGN KEY FK_6544F36312469DE2');
        $this->addSql('ALTER TABLE categories_products DROP FOREIGN KEY FK_6544F3634584665A');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE categories_products');
        $this->addSql('DROP TABLE product');
    }
}
