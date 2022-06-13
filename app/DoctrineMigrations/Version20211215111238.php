<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20211215111238
 */
class Version20211215111238 extends AbstractMigration
{
    /**
     * @param Schema $schema
     *
     * @throws AbortMigrationException
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE dwolla_event (id VARCHAR(255) NOT NULL, created DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', received DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', topic VARCHAR(255) NOT NULL, resource_id VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bookstore_transfer (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, net INT NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime)\', INDEX IDX_B4FAA418727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bookstore_transfer ADD CONSTRAINT FK_B4FAA418727ACA70 FOREIGN KEY (parent_id) REFERENCES bookstore_transfer (id)');
        $this->addSql('ALTER TABLE user_transaction ADD unprocessed_amount INT DEFAULT 0 NOT NULL');
    }

    /**
     * @param Schema $schema
     *
     * @throws AbortMigrationException
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bookstore_transfer DROP FOREIGN KEY FK_B4FAA418727ACA70');
        $this->addSql('DROP TABLE dwolla_event');
        $this->addSql('DROP TABLE bookstore_transfer');
        $this->addSql('ALTER TABLE user_transaction DROP unprocessed_amount');
    }
}
