<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220218100612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Questions Добавил FULLTEXT индексы на поля title и text';
    }

    /**
     * @return void Migration UP
     */
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE FULLTEXT INDEX `questions_answer_text` ON `questions_answer` (`text`)');
        $this->addSql('CREATE FULLTEXT INDEX `questions_question_title` ON `questions_question` (`title`)');
        $this->addSql('CREATE FULLTEXT INDEX `questions_question_text` ON `questions_question` (`text`)');
    }

    /**
     * @return void Migration DOWN
     */
    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX `questions_answer_text` ON `questions_answer`');
        $this->addSql('DROP INDEX `questions_question_title` ON `questions_question`');
        $this->addSql('DROP INDEX `questions_question_text` ON `questions_question`');
    }
}
