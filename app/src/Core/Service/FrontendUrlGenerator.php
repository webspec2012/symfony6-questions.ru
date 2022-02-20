<?php
namespace App\Core\Service;

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
     * Конструктор
     *
     * @param UrlGeneratorInterface $urlGenerator Url Generator
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
    )
    {
        $this->urlGenerator = $urlGenerator;
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
        return parse_url($this->getAbsoluteUrl($routeId, $params), PHP_URL_PATH);
    }
}
