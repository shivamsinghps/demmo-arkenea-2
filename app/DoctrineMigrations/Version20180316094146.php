<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180316094146 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_school (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, active SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_transaction (id INT AUTO_INCREMENT NOT NULL, sender_id INT DEFAULT NULL, campaign_id INT DEFAULT NULL, order_id INT DEFAULT NULL, recipient_id INT DEFAULT NULL, external_id VARCHAR(50) DEFAULT NULL, type SMALLINT NOT NULL, amount INT NOT NULL, fee INT NOT NULL, net INT NOT NULL, comment TEXT DEFAULT NULL, date DATETIME NOT NULL, INDEX FK_transaction_user (sender_id), INDEX FK_transaction_donor (recipient_id), INDEX IX_transaction_type (type), INDEX FK_transaction_order (order_id), INDEX FK_transaction_campaign (campaign_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campaign_contact (id INT AUTO_INCREMENT NOT NULL, campaign_id INT DEFAULT NULL, contact_id INT DEFAULT NULL, status SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX FK_campaign (campaign_id), INDEX FK_campaign_contact (contact_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_item (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, book_id INT DEFAULT NULL, sku VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, price INT NOT NULL, quantity INT NOT NULL, status SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX FK_item_order (order_id), INDEX FK_item_book (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, country VARCHAR(2) NOT NULL, region VARCHAR(50) DEFAULT NULL, city VARCHAR(50) NOT NULL, code VARCHAR(10) NOT NULL, address1 VARCHAR(255) NOT NULL, address2 VARCHAR(255) DEFAULT NULL, checksum VARCHAR(32) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX IX_unique_checksum (checksum), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_major (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, active SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campaign_book (id INT AUTO_INCREMENT NOT NULL, campaign_id INT DEFAULT NULL, external_id VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) DEFAULT NULL, class VARCHAR(255) DEFAULT NULL, isbn VARCHAR(15) DEFAULT NULL, price INT NOT NULL, quantity INT NOT NULL, status SMALLINT NOT NULL, state SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IX_book_status (status), INDEX IX_book_campaign (campaign_id), UNIQUE INDEX IX_unique_book (external_id, campaign_id, state), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_contacts (id INT AUTO_INCREMENT NOT NULL, student_id INT DEFAULT NULL, donor_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX FK_contact_user (student_id), INDEX FK_contact_donor (donor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, campaign_id INT DEFAULT NULL, address_id INT DEFAULT NULL, external_id VARCHAR(50) DEFAULT NULL, price INT NOT NULL, shipping INT NOT NULL, tax INT NOT NULL, status SMALLINT NOT NULL, submitted DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX FK_order_user (user_id), INDEX FK_order_address (address_id), INDEX FK_order_campaign (campaign_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campaign (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, estimated_shipping INT DEFAULT NULL, estimated_tax INT DEFAULT NULL, estimated_cost INT DEFAULT NULL, funded_total INT NOT NULL, status INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IX_campaign_status (status), INDEX FK_campaign_user (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_avatar (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, comment TEXT DEFAULT NULL, status SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, profile_id INT DEFAULT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(100) NOT NULL, role VARCHAR(50) NOT NULL, active SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX FK_user_profile (profile_id), UNIQUE INDEX IX_unique_login (login), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_profile (id INT AUTO_INCREMENT NOT NULL, school_id INT DEFAULT NULL, major_id INT DEFAULT NULL, address_id INT DEFAULT NULL, avatar_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, grad_year INT NOT NULL, student_id VARCHAR(255) DEFAULT NULL, about TEXT DEFAULT NULL, visible SMALLINT NOT NULL, facebook SMALLINT NOT NULL, twitter SMALLINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX FK_profile_school (school_id), INDEX FK_profile_major (major_id), INDEX FK_profile_address (address_id), INDEX FK_profile_avatar (avatar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_transaction ADD CONSTRAINT FK_DB2CCC44F624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_transaction ADD CONSTRAINT FK_DB2CCC44F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE user_transaction ADD CONSTRAINT FK_DB2CCC448D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE user_transaction ADD CONSTRAINT FK_DB2CCC44E92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE campaign_contact ADD CONSTRAINT FK_E4D87A14F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE campaign_contact ADD CONSTRAINT FK_E4D87A14E7A1254A FOREIGN KEY (contact_id) REFERENCES user_contacts (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F098D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_item ADD CONSTRAINT FK_52EA1F0916A2B381 FOREIGN KEY (book_id) REFERENCES campaign_book (id)');
        $this->addSql('ALTER TABLE campaign_book ADD CONSTRAINT FK_CA0298D3F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE user_contacts ADD CONSTRAINT FK_D3CDF173CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_contacts ADD CONSTRAINT FK_D3CDF1733DD7B7A7 FOREIGN KEY (donor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE campaign ADD CONSTRAINT FK_1F1512DDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649CCFA12B8 FOREIGN KEY (profile_id) REFERENCES user_profile (id)');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405C32A47EE FOREIGN KEY (school_id) REFERENCES user_school (id)');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405E93695C7 FOREIGN KEY (major_id) REFERENCES user_major (id)');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB40586383B10 FOREIGN KEY (avatar_id) REFERENCES user_avatar (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405C32A47EE');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398F5B7AF75');
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405F5B7AF75');
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405E93695C7');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F0916A2B381');
        $this->addSql('ALTER TABLE campaign_contact DROP FOREIGN KEY FK_E4D87A14E7A1254A');
        $this->addSql('ALTER TABLE user_transaction DROP FOREIGN KEY FK_DB2CCC448D9F6D38');
        $this->addSql('ALTER TABLE order_item DROP FOREIGN KEY FK_52EA1F098D9F6D38');
        $this->addSql('ALTER TABLE user_transaction DROP FOREIGN KEY FK_DB2CCC44F639F774');
        $this->addSql('ALTER TABLE campaign_contact DROP FOREIGN KEY FK_E4D87A14F639F774');
        $this->addSql('ALTER TABLE campaign_book DROP FOREIGN KEY FK_CA0298D3F639F774');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398F639F774');
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB40586383B10');
        $this->addSql('ALTER TABLE user_transaction DROP FOREIGN KEY FK_DB2CCC44F624B39D');
        $this->addSql('ALTER TABLE user_transaction DROP FOREIGN KEY FK_DB2CCC44E92F8F78');
        $this->addSql('ALTER TABLE user_contacts DROP FOREIGN KEY FK_D3CDF173CB944F1A');
        $this->addSql('ALTER TABLE user_contacts DROP FOREIGN KEY FK_D3CDF1733DD7B7A7');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('ALTER TABLE campaign DROP FOREIGN KEY FK_1F1512DDA76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649CCFA12B8');
        $this->addSql('DROP TABLE user_school');
        $this->addSql('DROP TABLE user_transaction');
        $this->addSql('DROP TABLE campaign_contact');
        $this->addSql('DROP TABLE order_item');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE user_major');
        $this->addSql('DROP TABLE campaign_book');
        $this->addSql('DROP TABLE user_contacts');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE campaign');
        $this->addSql('DROP TABLE user_avatar');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_profile');
    }
}
