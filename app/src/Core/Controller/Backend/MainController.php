<?php
namespace App\Core\Controller\Backend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Основной контроллер backend приложения
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
