<?php
namespace App\Controller\Backend;

use App\Core\Dto\DtoInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
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
        try {
            $factory = $this->container->get('form.factory');
            if (!$factory instanceof FormFactoryInterface) {
                throw new \LogicException(sprintf("Failed init form.factory for '%s'", $type));
            }

            return $factory->createNamed($name, $type, $data, $options);
        } catch (\Throwable $e) {
            throw new \LogicException(
                message: $e->getMessage(),
                code: (int) $e->getCode(),
                previous: $e->getPrevious()
            );
        }
    }

    /**
     * Проверка CSRF токена
     *
     * @param Request $request Request
     * @return void
     */
    protected function checkCsrfToken(Request $request): void
    {
        if (!$this->isCsrfTokenValid($this->csrfTokenName, (string) $request->request->get('_csrf_token'))) {
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
