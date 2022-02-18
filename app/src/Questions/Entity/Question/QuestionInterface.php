<?php
namespace App\Questions\Entity\Question;

use DateTime;
use App\Questions\Entity\Category\CategoryInterface;
use App\Users\Entity\UserInterface;

/**
 * Интерфейс для сущности "Вопрос".
 */
interface QuestionInterface
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
     * @return CategoryInterface Категория
     */
    public function getCategory(): CategoryInterface;

    /**
     * @return string Заголовок
     */
    public function getTitle(): string;

    /**
     * @return string Текст
     */
    public function getText(): string;

    /**
     * @return string Slug
     */
    public function getSlug(): string;

    /**
     * @return string Ссылка
     */
    public function getHref(): string;

    /**
     * @return int Количество опубликованных ответов
     */
    public function getTotalPublishedAnswers(): int;

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
