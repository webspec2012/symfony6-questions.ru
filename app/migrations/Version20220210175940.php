<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220210175940 extends AbstractMigration
{
    /**
     * @return string Description
     */
    public function getDescription(): string
    {
        return 'INIT Создание таблицы sessions';
    }

    /**
     * @return void Migration UP
     */
    public function up(Schema $schema): void
    {
        // table sessions
        $this->addSql('CREATE TABLE `sessions` (
            `sess_id` VARBINARY(128) NOT NULL PRIMARY KEY,
            `sess_data` BLOB NOT NULL,
            `sess_lifetime` INTEGER UNSIGNED NOT NULL,
            `sess_time` INTEGER UNSIGNED NOT NULL,
            INDEX `sessions_sess_lifetime_idx` (`sess_lifetime`)
        ) COLLATE utf8mb4_bin, ENGINE = InnoDB;');
    }

    /**
     * @return void Migration DOWN
     */
    public function down(Schema $schema): void
    {
        // table sessions
        $this->addSql('DROP TABLE `sessions`');
    }
}
