<?php
namespace App\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Основной контроллер frontend приложения
 */
final class SiteController extends AppController
{
    /**
     * Главная страница приложения
     *
     * @Route("/", name="index")
     *
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('site/index');
    }
}
