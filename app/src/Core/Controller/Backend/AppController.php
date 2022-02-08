<?php
namespace App\Core\Controller\Backend;

use App\Core\Controller\CommonController;

/**
 * Базовый контроллер backend приложения
 */
abstract class AppController extends CommonController
{
    /**
     * @inheritdoc
     */
    protected function renderView(string $view, array $parameters = []): string
    {
        return parent::renderView('@backend/'.$view.'.html.twig', $parameters);
    }
}
