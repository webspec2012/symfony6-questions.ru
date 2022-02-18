<?php
namespace App\Questions\Entity\Answer;

use DateTime;
use App\Questions\Entity\Question\QuestionInterface;
use App\Users\Entity\UserInterface;

/**
 * Интерфейс для сущности "Ответ".
 */
interface AnswerInterface
{
    /**
     * @const string Статус "Опубликован"
     */
    public const STATUS_PUBLISHED = 'PUBLISHED';

    /**
     * @const string Статус "Неопубликован"
     */
    public const STATUS_UNPUBLISHED = 'UNPUBLISHED';

    /**
     * @const string Статус "Удален"
     */
    public const STATUS_DELETED = 'DELETED';

    /**
     * @return int ID
     */
    public function getId(): int;

    /**
     * @return string Статус
     */
    public function getStatus(): string;

    /**
     * @return QuestionInterface Вопрос
     */
    public function getQuestion(): QuestionInterface;

    /**
     * @return string Текст
     */
    public function getText(): string;

    /**
     * @return UserInterface|null Автор
     */
    public function getCreatedBy(): ?UserInterface;

    /**
     * @return string IP адрес Автора
     */
    public function getCreatedByIp(): string;

    /**
     * @return DateTime|null Дата создания
     */
    public function getCreatedAt(): ?DateTime;

    /**
     * @return bool Опубликован?
     */
    public function isPublished(): bool;

    /**
     * @return bool Удален?
     */
    public function isDeleted(): bool;
}
