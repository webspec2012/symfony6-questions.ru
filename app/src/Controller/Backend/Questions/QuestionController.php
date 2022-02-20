<?php
namespace App\Controller\Backend\Questions;

use App\Controller\Backend\AppController;
use App\Core\Exception\AppException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Questions\Dto\Answer\AnswerCreateForm;
use App\Questions\Dto\Answer\AnswerSearchForm;
use App\Questions\Dto\Question\QuestionUpdateForm;
use App\Questions\Form\Answer\AnswerCreateFormType;
use App\Questions\Form\Answer\AnswerSearchFormType;
use App\Questions\Form\Question\QuestionCreateFormType;
use App\Questions\Form\Question\QuestionSearchFormType;
use App\Questions\Form\Question\QuestionUpdateFormType;
use App\Questions\UseCase\Answer\AnswerCreateCase;
use App\Questions\UseCase\Answer\AnswerFindCase;
use App\Questions\UseCase\Answer\AnswerListingCase;
use App\Questions\UseCase\Question\QuestionCreateCase;
use App\Questions\UseCase\Question\QuestionFindCase;
use App\Questions\UseCase\Question\QuestionListingCase;
use App\Questions\UseCase\Question\QuestionSwitchStatusCase;
use App\Questions\UseCase\Question\QuestionUpdateCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

/**
 * Контроллер для управления вопросами.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @IsGranted("ROLE_MANAGER_QUESTIONS")
 *
 * @Route("/questions/question", name="questions_question_")
 */
final class QuestionController extends AppController
{
    /**
     * @inheritdoc
     */
    protected string $csrfTokenName = 'questions_question';

    /**
     * @inheritdoc
     */
    protected string $routePrefix = 'backend_questions_question_';

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
     * Создание вопроса.
     *
     * @Route("/create/", name="create")
     *
     * @param Request $request Request
     * @param QuestionCreateCase $questionCreateCase Question Create Case
     *
     * @return Response Response
     */
    public function create(
        Request $request,
        QuestionCreateCase $questionCreateCase,
    ): Response
    {
        $form = $this->createForm(QuestionCreateFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $question = $questionCreateCase->create($form->getData());
                $this->addFlash('success', "Вопрос успешно создан.");

                return $this->redirectToRoute($this->getRoute('view'), ['id' => $question->getId()]);
            } catch (AppException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                $this->logger->error(__METHOD__.': '.$e->getMessage());
                $this->addFlash('error', "Произошла ошибка в процессе создания вопроса. Попробуйте позже.");
            }
        }

        return $this->render('questions/question/create', [
            'createForm' => $form->createView(),
        ]);
    }

    /**
     * Листинг категорий.
     *
     * @Route("/list/", name="list")
     *
     * @param Request $request Request
     * @param QuestionListingCase $questionListingCase Question Listing Case
     *
     * @return Response Response
     */
    public function list(
        Request $request,
        QuestionListingCase $questionListingCase,
    ): Response
    {
        $form = $this->createNamedForm('', QuestionSearchFormType::class);
        $form->submit(array_diff_key($request->query->all(), array_flip(['page'])));
        $filters = $form->isSubmitted() && $form->isValid() ? (array) $form->getData() : [];

        try {
            $page = (int) $request->query->get('page', 1);
            $paginator = $questionListingCase->listingWithPaginate($form->getData(), $page);
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
            $paginator = null;
        }

        return $this->render('questions/question/list', [
            'filterForm' => $form->createView(),
            'filters' => $filters,
            'paginator' => $paginator,
        ]);
    }

    /**
     * Просмотр информации о вопросе.
     *
     * @Route("/view/{id}/", name="view")
     *
     * @param int $id ID вопроса
     * @param Request $request Request
     * @param QuestionFindCase $questionFindCase Question Find Case
     * @param AnswerFindCase $answerFindCase Answer Find Case
     * @param AnswerListingCase $answerListingCase Answer Listing Case
     * @param AnswerCreateCase $answerCreateCase Answer Create Case
     *
     * @return Response
     */
    public function view(
        int $id,
        Request $request,
        QuestionFindCase $questionFindCase,
        AnswerFindCase $answerFindCase,
        AnswerListingCase $answerListingCase,
        AnswerCreateCase $answerCreateCase,
    ): Response
    {
        // for view
        try {
            $question = $questionFindCase->getQuestionById($id, false);
        } catch (NotFoundEntityException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        // =====

        // for answers listing
        $listingForm = $this->createNamedForm('', AnswerSearchFormType::class);
        $listingForm->submit(array_diff_key($request->query->all(), array_flip(['page'])));
        if ($listingForm->isSubmitted() && $listingForm->isValid() ) {
            $filters = array_merge(compact('id'), (array) $listingForm->getData());
        }

        /* @var AnswerSearchForm $formData */
        $formData = $listingForm->getData();
        $formData->question = $question->getId();
        $formData->orderBy = 'u.id_ASC';

        try {
            $page = (int) $request->query->get('page', 1);
            $paginator = $answerListingCase->listingWithPaginate($formData, $page);
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
            $paginator = null;
        }
        // =====

        // for create answer
        $createFormData = new AnswerCreateForm();
        $createFormData->question = $question->getId();

        $createAnswerForm = $this->createForm(AnswerCreateFormType::class, $createFormData);
        $createAnswerForm->handleRequest($request);
        if ($createAnswerForm->isSubmitted() && $createAnswerForm->isValid()) {
            try {
                $answerCreateCase->create($createAnswerForm->getData());
                $this->addFlash('success', "Ответ успешно добавлен.");

                return $this->redirectToRoute($this->getRoute('view'), ['id' => $id]);
            } catch (AppException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                $this->logger->error(__METHOD__.': '.$e->getMessage());
                $this->addFlash('error', "Произошла ошибка в процессе добавления вопроса. Попробуйте позже.");
            }
        }

        // =====

        return $this->render('questions/question/view', [
            // for view
            'question' => $question,

            // for answers listing
            'filterForm' => $listingForm->createView(),
            'filters' => $filters ?? [],
            'paginator' => $paginator,

            // for create answer
            'createForm' => $createAnswerForm->createView(),
        ]);
    }

    /**
     * Редактирование информации о вопросе.
     *
     * @Route("/update/{id}/", name="update")
     *
     * @param int $id ID вопроса
     * @param Request $request Request
     * @param QuestionFindCase $questionFindCase Question Find Case
     * @param QuestionUpdateCase $questionUpdateCase Question Update Case
     *
     * @return Response
     */
    public function update(
        int $id,
        Request $request,
        QuestionFindCase $questionFindCase,
        QuestionUpdateCase $questionUpdateCase,
    ): Response
    {
        try {
            $question = $questionFindCase->getQuestionById($id, false);
            if ($question->isDeleted()) {
                throw new NotFoundEntityException("Вопрос удалён, редактирование запрещено.");
            }
        } catch (NotFoundEntityException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $form = $this->createForm(QuestionUpdateFormType::class, new QuestionUpdateForm($question));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$questionUpdateCase->update($form->getData())) {
                    throw new ServiceException("Ошибка в процессе обновления вопроса. Попробуйте позже.");
                }

                $this->addFlash('success', 'Информация о вопросе успешно обновлена.');

                return $this->redirectToRoute($this->getRoute('view'), compact('id'));
            } catch (AppException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                $this->logger->error(__METHOD__.': '.$e->getMessage());
                $this->addFlash('error', "Произошла ошибка в процессе сохранения. Попробуйте позже.");
            }
        }

        return $this->render('questions/question/update', [
            'updateForm' => $form->createView(),
            'question' => $question,
        ]);
    }

    /**
     * Публикация вопроса
     *
     * @Route("/publish/{id}/", methods="POST", name="publish")
     *
     * @param int $id ID вопроса
     * @param Request $request Request
     * @param QuestionSwitchStatusCase $questionSwitchStatusCase Question Switch Status Case
     *
     * @return Response
     */
    public function publish(
        int $id,
        Request $request,
        QuestionSwitchStatusCase $questionSwitchStatusCase,
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            if (!$questionSwitchStatusCase->publish($id)) {
                throw new ServiceException("Ошибка в процессе публикации вопроса. Попробуйте позже.");
            }

            $this->addFlash('success', 'Вопрос успешно опубликован!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());
            $this->addFlash('error', "Произошла ошибка в процессе изменения статуса вопроса. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }

    /**
     * Снятие с публикации вопроса
     *
     * @Route("/unpublish/{id}/", methods="POST", name="unpublish")
     *
     * @param int $id ID вопроса
     * @param Request $request Request
     * @param QuestionSwitchStatusCase $questionSwitchStatusCase Question Switch Status Case
     *
     * @return Response
     */
    public function unpublish(
        int $id,
        Request $request,
        QuestionSwitchStatusCase $questionSwitchStatusCase,
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            if (!$questionSwitchStatusCase->unpublish($id)) {
                throw new ServiceException("Ошибка в процессе снятия с публикации вопроса. Попробуйте позже.");
            }

            $this->addFlash('success', 'Вопрос успешно снят с публикации!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());
            $this->addFlash('error', "Произошла ошибка в процессе изменения статуса вопроса. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }

    /**
     * Удаление вопроса
     *
     * @Route("/delete/{id}/", methods="POST", name="delete")
     *
     * @param int $id ID вопроса
     * @param Request $request Request
     * @param QuestionSwitchStatusCase $questionSwitchStatusCase Question Switch Status Case
     *
     * @return Response
     */
    public function delete(
        int $id,
        Request $request,
        QuestionSwitchStatusCase $questionSwitchStatusCase,
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            if (!$questionSwitchStatusCase->delete($id)) {
                throw new ServiceException("Ошибка в процессе удаления вопроса. Попробуйте позже.");
            }

            $this->addFlash('success', 'Вопрос успешно удален!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());
            $this->addFlash('error', "Произошла ошибка в процессе изменения статуса вопроса. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }
}
