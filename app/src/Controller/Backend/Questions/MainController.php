<?php
namespace App\Controller\Backend\Questions;

use App\Controller\Backend\AppController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

/**
 * Основной контроллер модуля.
 * Тут находится то, что не вошло в отдельные контроллеры
 *
 * @IsGranted("ROLE_MANAGER_QUESTIONS")
 *
 * @Route("/questions/main", name="questions_main_")
 */
final class MainController extends AppController
{
    /**
     * @inheritdoc
     */
    protected string $csrfTokenName = 'questions_main';

    /**
     * @inheritdoc
     */
    protected string $routePrefix = 'backend_questions_main_';

    /**
     * Dashboard
     *
     * @Route("/", name="dashboard")
     *
     * @return Response
     */
    public function dashboard(): Response
    {
        return $this->render('questions/main/dashboard');
    }
}
