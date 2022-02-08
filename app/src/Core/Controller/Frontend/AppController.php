<?php
namespace App\Core\Controller\Frontend;

use App\Core\Controller\CommonController;

/**
 * Базовый контроллер frontend приложения
 */
abstract class AppController extends CommonController
{
    /**
     * @inheritdoc
     */
    protected function renderView(string $view, array $parameters = []): string
    {
        return parent::renderView('@frontend/'.$view.'.html.twig', $parameters);
    }
}
