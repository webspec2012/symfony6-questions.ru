<?php
namespace App\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Базовый контроллер backend приложения
 */
abstract class AppController extends AbstractController
{
    /**
     * @var string CSRF Token name
     */
    protected string $csrfTokenName = 'default';

    /**
     * @var string Route Prefix
     */
    protected string $routePrefix = 'default';

    /**
     * @inheritdoc
     */
    protected function renderView(string $view, array $parameters = []): string
    {
        return parent::renderView('@backend/'.$view.'.html.twig', $parameters);
    }

    /**
     * Создание формы с указанием имени (по аналогии с createForm)
     *
     * @param string $name Название формы
     * @param string $type Название класса формы
     * @param array $data Данные для маппинга
     * @param array $options Конфигурация
     * @return FormInterface Объект формы
     */
    protected function createNamedForm(string $name, string $type, $data = null, array $options = []): FormInterface
    {
        return $this->container->get('form.factory')->createNamed($name, $type, $data, $options);
    }

    /**
     * Проверка CSRF токена
     *
     * @param Request $request Request
     * @return void
     */
    protected function checkCsrfToken(Request $request)
    {
        if (!$this->isCsrfTokenValid($this->csrfTokenName, $request->request->get('_csrf_token'))) {
            throw new AccessDeniedException("CSRF check failed");
        }
    }

    /**
     * Сформировать ссылку на абсолютный путь контроллера
     *
     * @param string $route Локальный путь
     * @return string
     */
    protected function getRoute(string $route): string
    {
        return $this->routePrefix.$route;
    }
}
