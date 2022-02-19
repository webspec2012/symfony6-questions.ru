<?php
namespace App\Controller\Frontend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Контроллер для работы с вопросами и ответами
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class QuestionController extends AppController
{
    /**
     * Листинг вопросов с фильтрацией по категориям.
     *
     * @Route("/", defaults={"category_slug": "", "page": "1"}, methods="GET", name="question_index")
     * @Route("/page/{page<[1-9]\d*>}/", defaults={"category_slug" : ""}, methods="GET", name="question_index_paginated")
     * @Route("/category/{category_slug}/", methods="GET", name="question_category")
     * @Route("/category/{category_slug}/{page<[1-9]\d*>}/", methods="GET", name="question_category_paginated")
     *
     * @param string $category_slug Slug категории
     * @param int $page Номер страницы
     *
     * @return Response
     */
    public function index(
        string $category_slug = '',
        int $page = 1,
    ): Response
    {
        return $this->render('site/index');
    }

    /**
     * Просмотр одного вопроса.
     *
     * @Route("/q/{id<[1-9]\d*>}_{slug}/", name="question_view")
     * @Route("/q/{id<[1-9]\d*>}_{slug}/{page<[1-9]\d*>}/", name="question_view_paginated")
     *
     * @param Request $request
     * @param int $id ID вопроса
     * @param string $slug Slug вопроса
     * @param int $page Номер страницы
     *
     * @return Response
     */
    public function view(
        Request $request,
        int $id,
        string $slug,
        int $page = 1,
    ): Response
    {
        return $this->render('site/index');
    }
}
