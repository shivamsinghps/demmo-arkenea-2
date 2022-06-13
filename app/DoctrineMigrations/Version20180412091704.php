<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180412091704 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX IX_unique_book ON campaign_book');
        $this->addSql('ALTER TABLE campaign_book ADD sku VARCHAR(255) NOT NULL, CHANGE external_id product_family_id VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX IX_unique_book ON campaign_book (product_family_id, sku, campaign_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX IX_unique_book ON campaign_book');
        $this->addSql('ALTER TABLE campaign_book ADD external_id VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP product_family_id, DROP sku');
        $this->addSql('CREATE UNIQUE INDEX IX_unique_book ON campaign_book (external_id, campaign_id, state)');
    }
}
