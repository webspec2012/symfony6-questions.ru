<?php
namespace App\Core\Controller\Frontend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Основной контроллер frontend приложения
 */
class MainController extends AppController
{
    /**
     * Главная страница приложения
     *
     * @Route("/", name="index")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('main/index');
    }
}
