<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220210080959 extends AbstractMigration
{
    /**
     * @return string Description
     */
    public function getDescription(): string
    {
        return 'INIT Создание таблиц user и messenger_messages';
    }

    /**
     * @return void Migration UP
     */
    public function up(Schema $schema): void
    {
        // table user
        $this->addSql('CREATE TABLE `user` (
            `id` INT AUTO_INCREMENT NOT NULL,
            `created_by_id` INT DEFAULT NULL,
            `updated_by_id` INT DEFAULT NULL,
            `username` VARCHAR(200) NOT NULL,
            `email` VARCHAR(180) NOT NULL,
            `email_verified` TINYINT(1) NOT NULL,
            `email_verified_token` VARCHAR(100) DEFAULT NULL,
            `email_subscribed` TINYINT(1) NOT NULL,
            `email_subscribed_token` VARCHAR(100) DEFAULT NULL,
            `password` VARCHAR(255) NOT NULL,
            `password_restore_token` VARCHAR(100) DEFAULT NULL,
            `is_admin` TINYINT(1) NOT NULL,
            `roles` JSON NOT NULL,
            `about` LONGTEXT NOT NULL,
            `created_at` DATETIME DEFAULT NULL,
            `updated_at` DATETIME DEFAULT NULL,
            `status` VARCHAR(50) NOT NULL,
            UNIQUE INDEX `UNIQ_8D93D649E7927C74` (`email`),
            UNIQUE INDEX `UNIQ_8D93D64944DB2A83` (`email_verified_token`),
            UNIQUE INDEX `UNIQ_8D93D6496EA0629F` (`email_subscribed_token`),
            UNIQUE INDEX `UNIQ_8D93D6495AE23700` (`password_restore_token`),
            INDEX `IDX_8D93D649B03A8386` (`created_by_id`),
            INDEX `IDX_8D93D649896DBBDE` (`updated_by_id`),
            INDEX `user_status` (`status`),
            PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT `FK_8D93D649B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT `FK_8D93D649896DBBDE` FOREIGN KEY (`updated_by_id`) REFERENCES `user` (`id`)');

        // table messenger_messages
        $this->addSql('CREATE TABLE `messenger_messages` (
            `id` BIGINT AUTO_INCREMENT NOT NULL,
            `body` LONGTEXT NOT NULL,
            `headers` LONGTEXT NOT NULL,
            `queue_name` VARCHAR(255) NOT NULL,
            `created_at` DATETIME NOT NULL,
            `available_at` DATETIME NOT NULL,
            `delivered_at` DATETIME DEFAULT NULL,
            INDEX `IDX_75EA56E016BA31DB` (`delivered_at`),
            PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    /**
     * @return void Migration DOWN
     */
    public function down(Schema $schema): void
    {
        // table user
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY `FK_8D93D649B03A8386`');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY `FK_8D93D649896DBBDE`');
        $this->addSql('DROP TABLE `user`');

        // table messenger_messages
        $this->addSql('DROP TABLE `messenger_messages`');
    }
}
