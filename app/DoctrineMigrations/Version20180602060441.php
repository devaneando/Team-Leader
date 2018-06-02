<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180602060441 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_promotion DROP FOREIGN KEY FK_AA48F3C34584665A');
        $this->addSql('DROP INDEX uniq_aa48f3c377153098 ON order_promotion');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7D9A062977153098 ON order_promotion (code)');
        $this->addSql('DROP INDEX idx_aa48f3c34584665a ON order_promotion');
        $this->addSql('CREATE INDEX IDX_7D9A06294584665A ON order_promotion (product_id)');
        $this->addSql('ALTER TABLE order_promotion ADD CONSTRAINT FK_AA48F3C34584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_promotion DROP FOREIGN KEY FK_7D9A06294584665A');
        $this->addSql('DROP INDEX uniq_7d9a062977153098 ON order_promotion');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA48F3C377153098 ON order_promotion (code)');
        $this->addSql('DROP INDEX idx_7d9a06294584665a ON order_promotion');
        $this->addSql('CREATE INDEX IDX_AA48F3C34584665A ON order_promotion (product_id)');
        $this->addSql('ALTER TABLE order_promotion ADD CONSTRAINT FK_7D9A06294584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }
}
