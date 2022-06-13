<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20200405124142 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user ADD statistic_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64953B6268F FOREIGN KEY (statistic_id) REFERENCES user_statistic (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64953B6268F ON user (statistic_id)');
        $this->addSql('ALTER TABLE user_statistic DROP FOREIGN KEY FK_647BCB78A76ED395');
        $this->addSql('DROP INDEX UNIQ_647BCB78A76ED395 ON user_statistic');
        $this->addSql('ALTER TABLE user_statistic DROP user_id');
        $this->addSql('DELETE FROM user_statistic');
        $this->addSql('INSERT INTO user_statistic (id) SELECT id FROM user');
        $this->addSql('UPDATE user SET statistic_id=id');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64953B6268F');
        $this->addSql('DROP INDEX UNIQ_8D93D64953B6268F ON user');
        $this->addSql('ALTER TABLE user DROP statistic_id');
        $this->addSql('ALTER TABLE user_statistic ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_statistic ADD CONSTRAINT FK_647BCB78A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_647BCB78A76ED395 ON user_statistic (user_id)');
    }
}
