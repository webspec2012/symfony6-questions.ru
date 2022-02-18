<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220218071158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'INIT QUESTIONS Создание таблиц category, question, answer';
    }

    /**
     * @return void Migration UP
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `questions_answer` (
            `id` INT AUTO_INCREMENT NOT NULL,
            `question_id` INT DEFAULT NULL,
            `status` VARCHAR(50) NOT NULL,
            `text` TEXT(5000) NOT NULL,

            `created_by` INT DEFAULT NULL,
            `created_by_ip` VARCHAR(45) DEFAULT NULL,
            `updated_by` INT DEFAULT NULL,
            `created_at` DATETIME DEFAULT NULL,
            `updated_at` DATETIME DEFAULT NULL,

            INDEX `IDX_83EB0FA21E27F6BF` (`question_id`),
            INDEX `questions_answer_status` (`status`),
            INDEX `IDX_83EB0FA2DE12AB56` (`created_by`),
            INDEX `questions_answer_created_by_ip` (`created_by_ip`),
            INDEX `IDX_83EB0FA216FE72E1` (`updated_by`),
            PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE `questions_category` (
            `id` INT AUTO_INCREMENT NOT NULL,
            `status` VARCHAR(50) NOT NULL,
            `title` VARCHAR(200) NOT NULL,
            `description` TEXT(5000) NOT NULL,
            `slug` VARCHAR(200) NOT NULL,
            `href` VARCHAR(250) NOT NULL,
            `total_published_questions` INT NOT NULL,

            `created_by` INT DEFAULT NULL,
            `created_by_ip` VARCHAR(45) DEFAULT NULL,
            `updated_by` INT DEFAULT NULL,
            `created_at` DATETIME DEFAULT NULL,
            `updated_at` DATETIME DEFAULT NULL,

            INDEX `questions_category_status` (`status`),
            INDEX `IDX_7438FB64DE12AB56` (`created_by`),
            INDEX `questions_category_created_by_ip` (`created_by_ip`),
            INDEX `IDX_7438FB6416FE72E1` (`updated_by`),
            UNIQUE INDEX `slug_unique` (`slug`),
            PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE `questions_question` (
            `id` INT AUTO_INCREMENT NOT NULL,
            `category_id` INT DEFAULT NULL,
            `created_by` INT DEFAULT NULL,
            `updated_by` INT DEFAULT NULL,
            `title` VARCHAR(250) NOT NULL,
            `text` TEXT(5000) NOT NULL,
            `total_published_answers` INT NOT NULL,
            `created_by_ip` VARCHAR(45) DEFAULT NULL,
            `created_at` DATETIME DEFAULT NULL,
            `updated_at` DATETIME DEFAULT NULL,
            `status` VARCHAR(50) NOT NULL,
            `slug` VARCHAR(200) NOT NULL,
            `href` VARCHAR(250) NOT NULL,
            INDEX `IDX_C483ABEB12469DE2` (`category_id`),
            INDEX `IDX_C483ABEBDE12AB56` (`created_by`),
            INDEX `IDX_C483ABEB16FE72E1` (`updated_by`),
            INDEX `questions_question_status` (`status`),
            INDEX `questions_question_created_by_ip` (`created_by_ip`),
            PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE `questions_answer` ADD CONSTRAINT `FK_83EB0FA21E27F6BF` FOREIGN KEY (`question_id`) REFERENCES `questions_question` (`id`) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `questions_answer` ADD CONSTRAINT `FK_83EB0FA2DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `questions_answer` ADD CONSTRAINT `FK_83EB0FA216FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `questions_category` ADD CONSTRAINT `FK_7438FB64DE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `questions_category` ADD CONSTRAINT `FK_7438FB6416FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `questions_question` ADD CONSTRAINT `FK_C483ABEB12469DE2` FOREIGN KEY (`category_id`) REFERENCES `questions_category` (`id`) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `questions_question` ADD CONSTRAINT `FK_C483ABEBDE12AB56` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE `questions_question` ADD CONSTRAINT `FK_C483ABEB16FE72E1` FOREIGN KEY (`updated_by`) REFERENCES `user` (`id`) ON DELETE SET NULL');

        $this->addSql('ALTER TABLE `user` ADD `created_by_ip` VARCHAR(45) DEFAULT NULL');
    }

    /**
     * @return void Migration DOWN
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `questions_question` DROP FOREIGN KEY `FK_C483ABEB12469DE2`');
        $this->addSql('ALTER TABLE `questions_answer` DROP FOREIGN KEY `FK_83EB0FA21E27F6BF`');

        $this->addSql('DROP TABLE `questions_answer`');
        $this->addSql('DROP TABLE `questions_category`');
        $this->addSql('DROP TABLE `questions_question`');

        $this->addSql('ALTER TABLE `user` DROP `created_by_ip`');
    }
}
