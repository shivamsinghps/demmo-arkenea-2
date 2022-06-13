<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200512102447 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE campaign ADD purchased_total INT DEFAULT 0 NOT NULL AFTER funded_total');
        $this->addSql('ALTER TABLE campaign ADD donations_from_previous INT DEFAULT 0 NOT NULL AFTER purchased_total');


        // update current data
        $this->addSql('UPDATE campaign c 
                            INNER JOIN ( SELECT campaign_id, SUM(ut.net) net_summ FROM user_transaction ut WHERE ut.type = 1 GROUP BY ut.campaign_id) ut_summ 
                            ON c.id = ut_summ.campaign_id
                            SET c.funded_total = net_summ');
        $this->addSql('UPDATE campaign c 
                            INNER JOIN ( SELECT campaign_id, SUM(ut.net) net_summ FROM user_transaction ut WHERE ut.type IN (2,3) GROUP BY ut.campaign_id) ut_summ 
                            ON c.id = ut_summ.campaign_id
                            SET c.purchased_total = net_summ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE campaign DROP donations_from_previous');
        $this->addSql('ALTER TABLE campaign DROP purchased_total');
    }
}
