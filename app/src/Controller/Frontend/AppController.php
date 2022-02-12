<?php
namespace App\Controller\Frontend;

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
     * @return Response Редирект на страницу профиля пользователя
     */
    protected function redirectToUserProfile(): Response
    {
        return $this->redirectToRoute('frontend_user_profile_main');
    }
}
