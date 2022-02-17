<?php
namespace App\Core\Service;

use App\Core\Dto\DtoInterface;
use App\Core\Exception\ServiceException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;

/**
 * Сервис валидации DTO
 */
final class ValidateDtoService
{
    /**
     * Практически любой DTO для сервиса содержит логику валидации этого самого DTO.
     * Данный метод проводит его валидацию.
     *
     * @param DtoInterface $dto Form
     * @throws ServiceException
     */
    public static function validateDto(DtoInterface $dto): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->addDefaultDoctrineAnnotationReader()
            ->getValidator();

        $violations = $validator->validate($dto);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                /* @var ConstraintViolation $violation */
                $errors[] = '['.$violation->getPropertyPath().'] '.$violation->getMessage();
            }

            throw new ServiceException(ValidateDtoService::formattedError($dto, $errors));
        }
    }

    /**
     * @param DtoInterface $dto Объект формы
     * @param string[] $errors Список ошибок к форме
     * @return string Форматирование вывода ошибки
     */
    public static function formattedError(DtoInterface $dto, array $errors): string
    {
        return sprintf(
            "Ошибка валидации '%s': \n\n%s",
            substr(strrchr(get_class($dto), "\\"), 1),
            implode("\n", $errors)
        );
    }
}
