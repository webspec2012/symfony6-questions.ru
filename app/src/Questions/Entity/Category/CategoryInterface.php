<?php
namespace App\Questions\Entity\Category;

/**
 * Интерфейс для сущности "Категория".
 */
interface CategoryInterface
{
    /**
     * @const string Статус "Опубликована"
     */
    public const STATUS_PUBLISHED = 'PUBLISHED';

    /**
     * @const string Статус "Неопубликована"
     */
    public const STATUS_UNPUBLISHED = 'UNPUBLISHED';

    /**
     * @const string Статус "Удалена"
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
     * @return string Название
     */
    public function getTitle(): string;

    /**
     * @return string Slug
     */
    public function getSlug(): string;

    /**
     * @return string Ссылка
     */
    public function getHref(): string;

    /**
     * @return string Описание
     */
    public function getDescription(): string;

    /**
     * @return int Общее количество вопросов
     */
    public function getTotalQuestions(): int;

    /**
     * @return int Количество опубликованных вопросов
     */
    public function getTotalPublishedQuestions(): int;

    /**
     * @return bool Опубликована?
     */
    public function isPublished(): bool;

    /**
     * @return bool Удалена?
     */
    public function isDeleted(): bool;
}
