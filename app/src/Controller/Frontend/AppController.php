<?php
namespace App\Controller\Frontend;

use App\Core\Dto\DtoInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Базовый контроллер frontend приложения
 */
abstract class AppController extends AbstractController
{
    /**
     * @inheritdoc
     */
    protected function renderView(string $view, array $parameters = []): string
    {
        return parent::renderView('@frontend/'.$view.'.html.twig', $parameters);
    }

    /**
     * @return Response Редирект на страницу где можно отобразить сообщение
     */
    protected function redirectToAuthbox(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToUserProfile();
        } else {
            return $this->redirectToRoute('frontend_user_login');
        }
    }

    /**
     * @return Response Редирект на страницу профиля пользователя
     */
    protected function redirectToUserProfile(): Response
    {
        return $this->redirectToRoute('frontend_user_profile_index');
    }

    /**
     * Form Load Data
     *
     * @param mixed $object Object
     * @param string $className ClassName
     * @return DtoInterface DTO object
     *
     * @psalm-template T
     * @psalm-param class-string<T> $className
     * @psalm-return T
     */
    protected function formLoadData(mixed $object, string $className)
    {
        if (!$object instanceof $className) {
            throw new \LogicException(sprintf("%s Failed load Data for '%s'", __METHOD__, $className));
        }

        return $object;
    }
}
