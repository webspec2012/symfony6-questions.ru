<?php
namespace App\Controller\Backend\Questions;

use App\Controller\Backend\AppController;
use App\Core\Exception\AppException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Questions\Dto\Answer\AnswerUpdateForm;
use App\Questions\Dto\Question\QuestionUpdateForm;
use App\Questions\Form\Answer\AnswerSearchFormType;
use App\Questions\Form\Answer\AnswerUpdateFormType;
use App\Questions\Form\Question\QuestionCreateFormType;
use App\Questions\Form\Question\QuestionSearchFormType;
use App\Questions\Form\Question\QuestionUpdateFormType;
use App\Questions\UseCase\Answer\AnswerFindCase;
use App\Questions\UseCase\Answer\AnswerListingCase;
use App\Questions\UseCase\Answer\AnswerSwitchStatusCase;
use App\Questions\UseCase\Answer\AnswerUpdateCase;
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
 * Контроллер для управления ответами.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @IsGranted("ROLE_MANAGER_QUESTIONS")
 *
 * @Route("/questions/answer", name="questions_answer_")
 */
final class AnswerController extends AppController
{
    /**
     * @inheritdoc
     */
    protected string $csrfTokenName = 'questions_answer';

    /**
     * @inheritdoc
     */
    protected string $routePrefix = 'backend_questions_answer_';

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
     * Листинг ответов.
     *
     * @Route("/list/", name="list")
     *
     * @param Request $request Request
     * @param AnswerListingCase $answerListingCase Answer Listing Case
     *
     * @return Response Response
     */
    public function list(
        Request $request,
        AnswerListingCase $answerListingCase,
    ): Response
    {
        $form = $this->createNamedForm('', AnswerSearchFormType::class);
        $form->submit(array_diff_key($request->query->all(), array_flip(['page'])));
        $filters = $form->isSubmitted() && $form->isValid() ? (array) $form->getData() : [];

        try {
            $page = (int) $request->query->get('page', 1);
            $paginator = $answerListingCase->listingWithPaginate($form->getData(), $page);
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
            $paginator = null;
        }

        return $this->render('questions/answer/list', [
            'filterForm' => $form->createView(),
            'filters' => $filters,
            'paginator' => $paginator,
        ]);
    }

    /**
     * Просмотр информации об ответе.
     *
     * @Route("/view/{id}/", name="view")
     *
     * @param int $id ID ответа
     * @param AnswerFindCase $answerFindCase Answer Find Case
     *
     * @return Response
     */
    public function view(
        int $id,
        AnswerFindCase $answerFindCase,
    ): Response
    {
        try {
            $answer = $answerFindCase->getAnswerById($id, false);
        } catch (NotFoundEntityException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return $this->render('questions/answer/view', compact('answer'));
    }

    /**
     * Редактирование информации об ответе.
     *
     * @Route("/update/{id}/", name="update")
     *
     * @param int $id ID ответа
     * @param Request $request Request
     * @param AnswerFindCase $answerFindCase Answer Find Case
     * @param AnswerUpdateCase $answerUpdateCase Answer Update Case
     *
     * @return Response
     */
    public function update(
        int $id,
        Request $request,
        AnswerFindCase $answerFindCase,
        AnswerUpdateCase $answerUpdateCase,
    ): Response
    {
        try {
            $answer = $answerFindCase->getAnswerById($id, false);
            if ($answer->isDeleted()) {
                throw new NotFoundEntityException("Ответ удалён, редактирование запрещено.");
            }
        } catch (NotFoundEntityException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $form = $this->createForm(AnswerUpdateFormType::class, new AnswerUpdateForm($answer));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$answerUpdateCase->update($form->getData())) {
                    throw new ServiceException("Ошибка в процессе обновления ответа. Попробуйте позже.");
                }

                $this->addFlash('success', 'Информация об ответе успешно обновлена.');

                return $this->redirectToRoute($this->getRoute('view'), compact('id'));
            } catch (AppException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                $this->logger->error(__METHOD__.': '.$e->getMessage());
                $this->addFlash('error', "Произошла ошибка в процессе сохранения. Попробуйте позже.");
            }
        }

        return $this->render('questions/answer/update', [
            'updateForm' => $form->createView(),
            'answer' => $answer,
        ]);
    }

    /**
     * Публикация ответа
     *
     * @Route("/publish/{id}/", methods="POST", name="publish")
     *
     * @param int $id ID ответа
     * @param Request $request Request
     * @param AnswerSwitchStatusCase $answerSwitchStatusCase Answer Switch Status Case
     *
     * @return Response
     */
    public function publish(
        int $id,
        Request $request,
        AnswerSwitchStatusCase $answerSwitchStatusCase,
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            if (!$answerSwitchStatusCase->publish($id)) {
                throw new ServiceException("Ошибка в процессе публикации ответа. Попробуйте позже.");
            }

            $this->addFlash('success', 'Ответ успешно опубликован!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());
            $this->addFlash('error', "Произошла ошибка в процессе изменения статуса ответа. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }

    /**
     * Снятие с публикации ответа
     *
     * @Route("/unpublish/{id}/", methods="POST", name="unpublish")
     *
     * @param int $id ID ответа
     * @param Request $request Request
     * @param AnswerSwitchStatusCase $answerSwitchStatusCase Answer Switch Status Case
     *
     * @return Response
     */
    public function unpublish(
        int $id,
        Request $request,
        AnswerSwitchStatusCase $answerSwitchStatusCase,
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            if (!$answerSwitchStatusCase->unpublish($id)) {
                throw new ServiceException("Ошибка в процессе снятия с публикации ответа. Попробуйте позже.");
            }

            $this->addFlash('success', 'Ответ успешно снят с публикации!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());
            $this->addFlash('error', "Произошла ошибка в процессе изменения статуса ответа. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }

    /**
     * Удаление ответа
     *
     * @Route("/delete/{id}/", methods="POST", name="delete")
     *
     * @param int $id ID ответа
     * @param Request $request Request
     * @param AnswerSwitchStatusCase $answerSwitchStatusCase Answer Switch Status Case
     *
     * @return Response
     */
    public function delete(
        int $id,
        Request $request,
        AnswerSwitchStatusCase $answerSwitchStatusCase,
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            if (!$answerSwitchStatusCase->delete($id)) {
                throw new ServiceException("Ошибка в процессе удаления ответа. Попробуйте позже.");
            }

            $this->addFlash('success', 'Ответ успешно удален!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error(__METHOD__.': '.$e->getMessage());
            $this->addFlash('error', "Произошла ошибка в процессе изменения статуса ответа. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }
}
