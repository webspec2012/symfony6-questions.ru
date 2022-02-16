<?php
namespace App\Controller\Frontend;

use App\Core\Exception\AppException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Users\Dto\User\UserChangePasswordForm;
use App\Users\Dto\User\UserProfileUpdateForm;
use App\Users\Form\User\UserChangePasswordFormType;
use App\Users\Form\User\UserProfileUpdateFormType;
use App\Users\UseCase\User\UserChangePasswordCase;
use App\Users\UseCase\User\UserFindCase;
use App\Users\UseCase\User\UserUpdateCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

/**
 * Контроллер для работы с профилем пользователя
 *
 * @IsGranted("ROLE_USER")
 *
 * @Route("/user/profile", name="user_profile_")
 */
final class UserProfileController extends AppController
{
    /**
     * @var UserFindCase User Find Case
     */
    private UserFindCase $userFindCase;

    /**
     * @var LoggerInterface Logger
     */
    private LoggerInterface $logger;

    /**
     * Конструктор
     *
     * @param UserFindCase $userFindCase User Find Case
     * @param LoggerInterface $logger Logger
     */
    public function __construct(
        UserFindCase $userFindCase,
        LoggerInterface $logger,
    )
    {
        $this->userFindCase = $userFindCase;
        $this->logger = $logger;
    }

    /**
     * @return Response Главная страница кабинета
     *
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        try {
            $user = $this->userFindCase->getUserByEmail($this->getUser()->getUserIdentifier());
        } catch (NotFoundEntityException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return $this->render('user-profile/index', compact('user'));
    }

    /**
     * Редактирование профиля пользователя
     *
     * @Route("/update/", name="update")
     *
     * @param Request $request Request
     *
     * @param UserUpdateCase $userUpdateCase User Update Case
     *
     * @return Response
     */
    public function update(
        Request $request,

        UserUpdateCase $userUpdateCase,
    ): Response
    {
        try {
            $user = $this->userFindCase->getUserByEmail($this->getUser()->getUserIdentifier());
        } catch (NotFoundEntityException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $form = $this->createForm(UserProfileUpdateFormType::class, new UserProfileUpdateForm($user));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$userUpdateCase->updateProfile($form->getData())) {
                    throw new ServiceException("Ошибка при обновлении профиля. Попробуйте позже.");
                }

                $this->addFlash('success', 'Профиль успешно обновлён!');

                return $this->redirectToRoute('frontend_user_profile_update');
            } catch (AppException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                $this->logger->error(__METHOD__.': '.$e->getMessage());
                $this->addFlash('error', "Произошла ошибка в процессе обновления профиля. Попробуйте позже.");
            }
        }

        return $this->render('user-profile/update', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Изменение пароля пользователю.
     *
     * @Route("/change-password/", name="change_password")
     *
     * @param Request $request Request
     *
     * @param UserChangePasswordCase $userChangePasswordCase User Change Password Case
     *
     * @return Response
     */
    public function changePassword(
        Request $request,

        UserChangePasswordCase $userChangePasswordCase,
    ): Response
    {
        try {
            $user = $this->userFindCase->getUserByEmail($this->getUser()->getUserIdentifier());
        } catch (NotFoundEntityException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $formData = new UserChangePasswordForm();
        $formData->id = $user->getId();

        $form = $this->createForm(UserChangePasswordFormType::class, $formData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$userChangePasswordCase->changePassword($form->getData())) {
                    throw new ServiceException("Ошибка при изменении пароля. Попробуйте позже.");
                }

                $this->addFlash('success', 'Пароль успешно изменен!');

                return $this->redirectToRoute('frontend_user_profile_index');
            } catch (AppException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                $this->logger->error(__METHOD__.': '.$e->getMessage());
                $this->addFlash('error', "Произошла ошибка в процессе изменения пароля. Попробуйте позже.");
            }
        }

        return $this->render('user-profile/change-password', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
