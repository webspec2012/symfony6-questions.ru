<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220211112014 extends AbstractMigration
{
    /**
     * @return string Description
     */
    public function getDescription(): string
    {
        return 'Обновление индексов для created_by и updated_by';
    }

    /**
     * @return void Migration UP
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649896DBBDE');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B03A8386');
        $this->addSql('DROP INDEX IDX_8D93D649896DBBDE ON user');
        $this->addSql('DROP INDEX IDX_8D93D649B03A8386 ON user');
        $this->addSql('ALTER TABLE user ADD created_by INT DEFAULT NULL, ADD updated_by INT DEFAULT NULL, DROP created_by_id, DROP updated_by_id');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DE12AB56 FOREIGN KEY (created_by) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64916FE72E1 FOREIGN KEY (updated_by) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_8D93D649DE12AB56 ON user (created_by)');
        $this->addSql('CREATE INDEX IDX_8D93D64916FE72E1 ON user (updated_by)');
    }

    /**
     * @return void Migration DOWN
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649DE12AB56');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D64916FE72E1');
        $this->addSql('DROP INDEX IDX_8D93D649DE12AB56 ON `user`');
        $this->addSql('DROP INDEX IDX_8D93D64916FE72E1 ON `user`');
        $this->addSql('ALTER TABLE `user` ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, DROP created_by, DROP updated_by, CHANGE username username VARCHAR(200) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email_verified_token email_verified_token VARCHAR(100) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email_subscribed_token email_subscribed_token VARCHAR(100) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password_restore_token password_restore_token VARCHAR(100) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE about about LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE status status VARCHAR(50) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D649896DBBDE ON `user` (updated_by_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B03A8386 ON `user` (created_by_id)');
    }
}
