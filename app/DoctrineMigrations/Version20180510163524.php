<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180510163524 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `order` ADD anonymous_token VARCHAR(255) DEFAULT NULL AFTER `user_id`');
        $this->addSql('ALTER TABLE `order` 
ADD transaction_fee INT NOT NULL AFTER `tax`, 
ADD fmt_fee INT NOT NULL AFTER `transaction_fee`, 
ADD total INT NOT NULL AFTER `fmt_fee`, 
CHANGE status status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE order_item CHANGE status status VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_item CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE `order` DROP transaction_fee, DROP fmt_fee, DROP total, CHANGE status status SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE `order` DROP anonymous_token');
    }
}
