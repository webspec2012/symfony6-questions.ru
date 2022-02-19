<?php
namespace App\Core\Service;

use App\Core\Exception\ServiceException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Сервис для формирования URL в frontend приложении
 */
class FrontendUrlGenerator
{
    /**
     * @var UrlGeneratorInterface Url Generator
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var ParameterBagInterface Params
     */
    private ParameterBagInterface $params;

    /**
     * @var string Frontend Host
     */
    private string $frontendHost;

    /**
     * Конструктор
     *
     * @param UrlGeneratorInterface $urlGenerator Url Generator
     * @param ParameterBagInterface $params Params
     * @throws ServiceException
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ParameterBagInterface $params,
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->params = $params;

        $this->setFrontendHost($this->params->get('app.frontend.host'));
    }

    /**
     * @param string $routeId Route Id
     * @param array $params Params for Route
     * @return string Сформированная ссылка
     */
    public function getAbsoluteUrl(string $routeId, array $params = []): string
    {
        return $this->urlGenerator->generate('frontend_'.$routeId, $params, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param string $routeId Route Id
     * @param array $params Params for Route
     * @return string Сформированная ссылка
     */
    public function getAbsolutePath(string $routeId, array $params = []): string
    {
        return str_replace($this->getFrontendHost(), "", $this->getAbsoluteUrl($routeId, $params));
    }

    /**
     * @param mixed $host Frontend Host
     * @return void
     * @throws ServiceException
     */
    private function setFrontendHost(mixed $host): void
    {
        if (!is_string($host)) {
            throw new ServiceException("Некорректное значение параметра 'frontendHost'");
        }

        $this->frontendHost = $host;
    }

    /**
     * @return string Frontend Host
     */
    private function getFrontendHost(): string
    {
        return $this->frontendHost;
    }
}
