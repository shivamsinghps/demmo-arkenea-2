<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20220312111238
 * @package Application\Migrations
 */
class Version20220312111238 extends AbstractMigration
{
    /**
     * @param Schema $schema
     *
     * @throws AbortMigrationException
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE user_transaction DROP unprocessed_amount');
        $this->addSql('ALTER TABLE `order` ADD unprocessed_amount INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE order_item ADD unprocessed_amount INT DEFAULT 0 NOT NULL');
    }

    /**
     * @param Schema $schema
     *
     * @throws AbortMigrationException
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE user_transaction ADD unprocessed_amount INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE `order` DROP unprocessed_amount');
        $this->addSql('ALTER TABLE order_item DROP unprocessed_amount');
    }
}
