<?php
namespace App\Questions\Service\SlugGenerate;

use App\Core\Exception\ServiceException;

/**
 * Простой вариант реализации формирования slug
 */
final class SimpleSlugGenerate implements SlugGenerateInterface
{
    /**
     * @var array Словарь транслитерации текста
     */
    public static array $slugTransliteratorDic = [
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'yo',  'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'i',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '',    'ы' => 'y',   'ъ' => '',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
    ];

    /**
     * @var int Дефолтная лимит слов
     */
    private int $limitDefault;

    /**
     * Конструктор
     *
     * @param int $limitDefault Дефолтная лимит слов
     */
    public function __construct(
        int $limitDefault= 5,
    )
    {
        $this->limitDefault = $limitDefault;
    }

    /**
     * @inheitdoc
     * @throws ServiceException
     */
    public function generate(string $text, ?int $limit = null): string
    {
        if ($limit === null) {
            $limit = $this->limitDefault;
        }

        try {
            $text = trim(strip_tags($text));
            if (empty($text)) {
                throw new ServiceException("Пустая исходная строка");
            }

            $text = mb_strtolower($text);
            $text = trim(preg_replace('/[-\s]+/', '-', $text), '-');
            $text = preg_replace('/[^-\p{L}0-9]+/u', '', $text);
            $text = strtr($text, SimpleSlugGenerate::$slugTransliteratorDic);
            $words = array_filter(explode('-', $text), function ($word) {
                return mb_strlen($word) > 2;
            });

            if ($limit) {
                $words = array_slice($words, 0, $limit);
            }

            return implode('-', $words);
        } catch (\Throwable $e) {
            throw new ServiceException(
                message: sprintf("Ошибка при формировании slug: %s", $e->getMessage()),
                code: (int) $e->getCode(),
                previous: $e
            );
        }
    }
}
