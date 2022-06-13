<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200710102432 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE campaign_contact DROP FOREIGN KEY FK_E4D87A14E7A1254A');
        $this->addSql('ALTER TABLE campaign_contact ADD CONSTRAINT FK_E4D87A14E7A1254A FOREIGN KEY (contact_id) REFERENCES user_contacts (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE campaign_contact DROP FOREIGN KEY FK_E4D87A14E7A1254A');
        $this->addSql('ALTER TABLE campaign_contact ADD CONSTRAINT FK_E4D87A14E7A1254A FOREIGN KEY (contact_id) REFERENCES user_contacts (id)');
    }
}
