<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200403135605 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE user_statistic');
        $this->addSql('CREATE TABLE user_statistic (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, students_founded INT DEFAULT 0 NOT NULL, books_purchased_for INT DEFAULT 0 NOT NULL, amount_founded INT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_647BCB78A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_statistic ADD CONSTRAINT FK_647BCB78A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('INSERT INTO user_statistic (id, user_id) SELECT id, id FROM user');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_statistic');
        $this->addSql('CREATE TABLE `user_statistic` (
  `user_id` int(11) NOT NULL,
  `students_founded` int(11) NOT NULL DEFAULT \'0\',
  `books_purchased_for` int(11) NOT NULL DEFAULT \'0\',
  `amount_founded` int(11) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`user_id`),
  CONSTRAINT `FK_647BCB78A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
        $this->addSql('INSERT INTO user_statistic (id) SELECT id FROM user');
    }
}
