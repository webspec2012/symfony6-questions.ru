<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220220072445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Questions Добавил счётчики totalQuestions и totalAnswers';
    }

    /**
     * @return void Migration UP
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `questions_category` ADD `total_questions` INT NOT NULL');
        $this->addSql('ALTER TABLE `questions_question` ADD `total_answers` INT NOT NULL');
    }

    /**
     * @return void Migration DOWN
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `questions_category` DROP `total_questions`');
        $this->addSql('ALTER TABLE `questions_question` DROP `total_answers`');
    }
}
