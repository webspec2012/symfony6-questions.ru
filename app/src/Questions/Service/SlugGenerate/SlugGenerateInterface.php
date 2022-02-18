<?php
namespace App\Questions\Service\SlugGenerate;

use App\Core\Exception\ServiceException;

/**
 * Интерфейс для сервиса генерации slug
 */
interface SlugGenerateInterface
{
    /**
     * @param string $text Исходный текст
     * @param int|null $limit Ограничение на количество слов (если null - по-умолчанию)
     * @return string slug на основе указанной текстовой строки
     * @throws ServiceException В случае ошибки
     */
    public function generate(string $text, ?int $limit = null): string;
}
