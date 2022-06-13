<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180511080242 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_transaction ADD anonymous SMALLINT NOT NULL');

        $this->addSql('ALTER TABLE `user_transaction` DROP FOREIGN KEY `FK_DB2CCC44F624B39D`');
        $this->addSql('DROP INDEX `fk_transaction_user` ON `user_transaction`');
        $this->addSql('CREATE INDEX `FK_transaction_sender` ON `user_transaction` (`sender_id`)');
        $this->addSql('ALTER TABLE `user_transaction` ADD CONSTRAINT `FK_DB2CCC44F624B39D` FOREIGN KEY `FK_transaction_sender` (`sender_id`) REFERENCES `user` (`id`)');

        $this->addSql('ALTER TABLE `user_transaction` DROP FOREIGN KEY `FK_DB2CCC44E92F8F78`');
        $this->addSql('DROP INDEX `fk_transaction_donor` ON `user_transaction`');
        $this->addSql('CREATE INDEX `FK_transaction_recipient` ON `user_transaction` (`recipient_id`)');
        $this->addSql('ALTER TABLE `user_transaction` ADD CONSTRAINT `FK_DB2CCC44E92F8F78` FOREIGN KEY `FK_transaction_recipient` (`recipient_id`) REFERENCES `user` (`id`)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_transaction DROP anonymous');

        $this->addSql('ALTER TABLE `user_transaction` DROP FOREIGN KEY `FK_DB2CCC44F624B39D`');
        $this->addSql('DROP INDEX `FK_transaction_sender` ON `user_transaction`');
        $this->addSql('CREATE INDEX `fk_transaction_user` ON `user_transaction` (`sender_id`)');
        $this->addSql('ALTER TABLE `user_transaction` ADD CONSTRAINT `FK_DB2CCC44F624B39D` FOREIGN KEY `fk_transaction_user` (`sender_id`) REFERENCES `user` (`id`)');

        $this->addSql('ALTER TABLE `user_transaction` DROP FOREIGN KEY `FK_DB2CCC44E92F8F78`');
        $this->addSql('DROP INDEX `FK_transaction_recipient` ON `user_transaction`');
        $this->addSql('CREATE INDEX `fk_transaction_donor` ON `user_transaction` (`recipient_id`)');
        $this->addSql('ALTER TABLE `user_transaction` ADD CONSTRAINT `FK_DB2CCC44E92F8F78` FOREIGN KEY `fk_transaction_donor` (`recipient_id`) REFERENCES `user` (`id`)');
    }
}
