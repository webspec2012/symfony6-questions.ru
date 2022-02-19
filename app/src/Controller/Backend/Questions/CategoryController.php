<?php
namespace App\Controller\Backend\Questions;

use App\Controller\Backend\AppController;
use App\Core\Exception\AppException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Questions\Dto\Category\CategoryUpdateForm;
use App\Questions\Entity\Question\QuestionInterface;
use App\Questions\Form\Category\CategoryCreateFormType;
use App\Questions\Form\Category\CategorySearchFormType;
use App\Questions\Form\Category\CategoryUpdateFormType;
use App\Questions\UseCase\Category\CategoryCreateCase;
use App\Questions\UseCase\Category\CategoryFindCase;
use App\Questions\UseCase\Category\CategoryListingCase;
use App\Questions\UseCase\Category\CategorySwitchStatusCase;
use App\Questions\UseCase\Category\CategoryUpdateCase;
use App\Questions\UseCase\Question\QuestionFindCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

/**
 * Контроллер для управления категориями вопросов.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @IsGranted("ROLE_MANAGER_QUESTIONS")
 *
 * @Route("/questions/category", name="questions_category_")
 */
final class CategoryController extends AppController
{
    /**
     * @inheritdoc
     */
    protected string $csrfTokenName = 'questions_category';

    /**
     * @inheritdoc
     */
    protected string $routePrefix = 'backend_questions_category_';

    /**
     * @var LoggerInterface Logger
     */
    private LoggerInterface $logger;

    /**
     * Конструктор
     *
     * @param LoggerInterface $logger Logger
     */
    public function __construct(
        LoggerInterface $logger,
    )
    {
        $this->logger = $logger;
    }

    /**
     * Создание категории.
     *
     * @Route("/create/", name="create")
     *
     * @param Request $request Request
     * @param CategoryCreateCase $categoryCreateCase Category Create Case
     *
     * @return Response Response
     */
    public function create(
        Request $request,
        CategoryCreateCase $categoryCreateCase,
    ): Response
    {
        $form = $this->createForm(CategoryCreateFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $category = $categoryCreateCase->create($form->getData());
                $this->addFlash('success', "Категория успешно создана.");

                return $this->redirectToRoute($this->getRoute('view'), ['id' => $category->getId()]);
            } catch (AppException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                $this->logger->error(__METHOD__.': '.$e->getMessage());
                $this->addFlash('error', "Произошла ошибка в процессе создания категории. Попробуйте позже.");
            }
        }

        return $this->render('questions/category/create', [
            'createForm' => $form->createView(),
        ]);
    }

    /**
     * Листинг категорий.
     *
     * @Route("/list/", name="list")
     *
     * @param Request $request Request
     * @param CategoryListingCase $categoryListingCase Category Listing Case
     *
     * @return Response Response
     */
    public function list(
        Request $request,
        CategoryListingCase $categoryListingCase,
    ): Response
    {
        $form = $this->createNamedForm('', CategorySearchFormType::class);
        $form->submit(array_diff_key($request->query->all(), array_flip(['page'])));
        $filters = $form->isSubmitted() && $form->isValid() ? (array) $form->getData() : [];

        try {
            $page = (int) $request->query->get('page', 1);
            $paginator = $categoryListingCase->listingWithPaginate($form->getData(), $page);
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
            $paginator = null;
        }

        return $this->render('questions/category/list', [
            'filterForm' => $form->createView(),
            'filters' => $filters,
            'paginator' => $paginator,
        ]);
    }

    /**
     * Просмотр информации о категории.
     *
     * @Route("/view/{id}/", name="view")
     *
     * @param int $id ID категории
     * @param CategoryFindCase $categoryFindCase Category Find Case
     * @param QuestionFindCase $questionFindCase Question Find Case
     *
     * @return Response
     */
    public function view(
        int $id,
        CategoryFindCase $categoryFindCase,
        QuestionFindCase $questionFindCase,
    ): Response
    {
        try {
            $category = $categoryFindCase->getCategoryById($id, false);
        } catch (NotFoundEntityException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $totalQuestions = $questionFindCase->countQuestionsByCategory($id, null);
        $publishedQuestions = $questionFindCase->countQuestionsByCategory($id, QuestionInterface::STATUS_PUBLISHED);

        return $this->render('questions/category/view', compact('category', 'totalQuestions', 'publishedQuestions'));
    }

    /**
     * Редактирование информации о категории.
     *
     * @Route("/update/{id}/", name="update")
     *
     * @param int $id ID категории
     * @param Request $request Request
     *
     * @param CategoryFindCase $categoryFindCase Category Find Case
     * @param CategoryUpdateCase $categoryUpdateCase Category Update Case
     *
     * @return Response
     */
    public function update(
        int $id,
        Request $request,
        CategoryFindCase $categoryFindCase,
        CategoryUpdateCase $categoryUpdateCase,
    ): Response
    {
        try {
            $category = $categoryFindCase->getCategoryById($id, false);
            if ($category->isDeleted()) {
                throw new NotFoundEntityException("Категория удалена, редактирование запрещено.");
            }
        } catch (NotFoundEntityException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $form = $this->createForm(CategoryUpdateFormType::class, new CategoryUpdateForm($category));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$categoryUpdateCase->update($form->getData())) {
                    throw new ServiceException("Ошибка в процессе обновления категории. Попробуйте позже.");
                }

                $this->addFlash('success', 'Информация о категории успешно обновлена.');

                return $this->redirectToRoute($this->getRoute('view'), compact('id'));
            } catch (AppException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                $this->logger->error(__METHOD__.': '.$e->getMessage());
                $this->addFlash('error', "Произошла ошибка в процессе сохранения. Попробуйте позже.");
            }
        }

        return $this->render('questions/category/update', [
            'updateForm' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * Публикация категории
     *
     * @Route("/publish/{id}/", methods="POST", name="publish")
     *
     * @param int $id ID категории
     * @param Request $request Request
     * @param CategorySwitchStatusCase $categorySwitchStatusCase Category Switch Status Case
     *
     * @return Response
     */
    public function publish(
        int $id,
        Request $request,
        CategorySwitchStatusCase $categorySwitchStatusCase,
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            if (!$categorySwitchStatusCase->publish($id)) {
                throw new ServiceException("Ошибка в процессе публикации категории. Попробуйте позже.");
            }

            $this->addFlash('success', 'Категория успешно опубликована!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());
            $this->addFlash('error', "Произошла ошибка в процессе изменения статуса категории. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }

    /**
     * Снятие с публикации категории
     *
     * @Route("/unpublish/{id}/", methods="POST", name="unpublish")
     *
     * @param int $id ID категории
     * @param Request $request Request
     * @param CategorySwitchStatusCase $categorySwitchStatusCase Category Switch Status Case
     *
     * @return Response
     */
    public function unpublish(
        int $id,
        Request $request,
        CategorySwitchStatusCase $categorySwitchStatusCase,
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            if (!$categorySwitchStatusCase->unpublish($id)) {
                throw new ServiceException("Ошибка в процессе снятия с публикации категории. Попробуйте позже.");
            }

            $this->addFlash('success', 'Категория успешно снята с публикации!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());
            $this->addFlash('error', "Произошла ошибка в процессе изменения статуса категории. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }

    /**
     * Удаление пользователя
     *
     * @Route("/delete/{id}/", methods="POST", name="delete")
     *
     * @param int $id ID категории
     * @param Request $request Request
     * @param CategorySwitchStatusCase $categorySwitchStatusCase Category Switch Status Case
     *
     * @return Response
     */
    public function delete(
        int $id,
        Request $request,
        CategorySwitchStatusCase $categorySwitchStatusCase,
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            if (!$categorySwitchStatusCase->delete($id)) {
                throw new ServiceException("Ошибка в процессе удаления категории. Попробуйте позже.");
            }

            $this->addFlash('success', 'Категория успешно удалена!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());
            $this->addFlash('error', "Произошла ошибка в процессе изменения статуса категории. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }
}
