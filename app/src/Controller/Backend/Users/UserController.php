<?php
namespace App\Controller\Backend\Users;

use App\Controller\Backend\AppController;
use App\Core\Exception\AppException;
use App\Core\Exception\NotFoundEntityException;
use App\Core\Exception\ServiceException;
use App\Users\Dto\User\UserChangePasswordForm;
use App\Users\Dto\User\UserCreateForm;
use App\Users\Dto\User\UserUpdateForm;
use App\Users\Form\User\UserCreateFormType;
use App\Users\Form\User\UserSearchFormType;
use App\Users\Form\User\UserUpdateFormType;
use App\Users\Service\PasswordGenerate\PasswordGenerateInterface;
use App\Users\UseCase\User\UserChangePasswordCase;
use App\Users\UseCase\User\UserCreateCase;
use App\Users\UseCase\User\UserFindCase;
use App\Users\UseCase\User\UserListingCase;
use App\Users\UseCase\User\UserSwitchStatusCase;
use App\Users\UseCase\User\UserUpdateCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

/**
 * Контроллер для управления пользователями.
 *
 * @IsGranted("ROLE_MANAGER_USERS")
 *
 * @Route("/users/user", name="users_user_")
 */
final class UserController extends AppController
{
    /**
     * @inheritdoc
     */
    protected string $csrfTokenName = 'users_user';

    /**
     * @inheritdoc
     */
    protected string $routePrefix = 'backend_users_user_';

    /**
     * Создание пользователя.
     *
     * @Route("/create/", name="create")
     *
     * @param Request $request Request
     * @param UserCreateCase $userCreateCase User Create Case
     * @param PasswordGenerateInterface $passwordGenerate Password Generate Service
     *
     * @return Response Response
     * @throws ServiceException
     */
    public function create(
        Request $request,
        UserCreateCase $userCreateCase,
        PasswordGenerateInterface $passwordGenerate,
    ): Response
    {
        $formData = new UserCreateForm();
        $formData->password = $passwordGenerate->generate();

        $form = $this->createForm(UserCreateFormType::class, $formData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user = $userCreateCase->create($form->getData());
                $this->addFlash('success', sprintf("Пользователь успешно создан. Пароль: '%s'", $formData->password));

                return $this->redirectToRoute($this->getRoute('view'), ['id' => $user->getId()]);
            } catch (AppException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('error', "Произошла ошибка в процессе создания пользователя. Попробуйте позже.");
            }
        }

        return $this->render('users/user/create', [
            'createForm' => $form->createView(),
        ]);
    }

    /**
     * Листинг пользоваталей.
     *
     * @Route("/list/", name="list")
     *
     * @param Request $request Request
     * @param UserListingCase $userListingCase User Listing Case
     *
     * @return Response Response
     */
    public function list(
        Request $request,
        UserListingCase $userListingCase
    ): Response
    {
        $form = $this->createNamedForm('', UserSearchFormType::class);
        $form->submit(array_diff_key($request->query->all(), array_flip(['page'])));
        $filters = $form->isSubmitted() && $form->isValid() ? (array) $form->getData() : [];

        try {
            $page = (int) $request->get('page', 1);
            $paginator = $userListingCase->listingWithPaginate($form->getData(), $page);
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
            $paginator = null;
        }

        return $this->render('users/user/list', [
            'filterForm' => $form->createView(),
            'filters' => $filters,
            'paginator' => $paginator,
        ]);
    }

    /**
     * Просмотр информации о пользователе.
     *
     * @Route("/view/{id}/", name="view")
     *
     * @param int $id ID пользователя
     * @param UserFindCase $userFindCase User Find Case
     *
     * @return Response
     */
    public function view(
        int $id,
        UserFindCase $userFindCase
    ): Response
    {
        try {
            $user = $userFindCase->getUserById($id, false);
        } catch (NotFoundEntityException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return $this->render('users/user/view', compact('user'));
    }

    /**
     * Редактирование информации о пользователе.
     *
     * @Route("/update/{id}/", name="update")
     *
     * @param int $id ID пользователя
     * @param Request $request Request
     * @param UserFindCase $userFindCase User Find Case
     * @param UserUpdateCase $userUpdateCase User Update Case
     *
     * @return Response
     */
    public function update(
        int $id,
        Request $request,
        UserFindCase $userFindCase,
        UserUpdateCase $userUpdateCase
    ): Response
    {
        try {
            $user = $userFindCase->getUserById($id);
        } catch (NotFoundEntityException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $formData = new UserUpdateForm();
        $formData->id = $user->getId();
        $formData->name = $user->getUsername();
        $formData->email = $user->getEmail();
        $formData->is_admin = $user->getIsAdmin();
        $formData->roles = $user->getRoles();
        $formData->about = $user->getAbout();

        $form = $this->createForm(UserUpdateFormType::class, $formData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $userUpdateCase->update($form->getData());
                $this->addFlash('success', 'Информация о пользователе успешно обновлена.');

                return $this->redirectToRoute($this->getRoute('view'), ['id' => $id]);
            } catch (AppException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Exception $e) {
                $this->addFlash('error', "Произошла ошибка в процессе сохранения. Попробуйте позже.");
            }
        }

        return $this->render('users/user/update', [
            'updateForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Блокировка пользователя
     *
     * @Route("/block/{id}/", methods="POST", name="block")
     *
     * @param int $id ID пользователя
     * @param Request $request Request
     * @param UserSwitchStatusCase $userSwitchStatusCase User Switch Status Case
     *
     * @return Response
     */
    public function block(
        int $id,
        Request $request,
        UserSwitchStatusCase $userSwitchStatusCase
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            $userSwitchStatusCase->block($id);

            $this->addFlash('success', 'Пользователь успешно заблокирован!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', "Произошла ошибка в процессе изменения статуса пользователю. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }

    /**
     * Удаление пользователя
     *
     * @Route("/delete/{id}/", methods="POST", name="delete")
     *
     * @param int $id ID пользователя
     * @param Request $request Request
     * @param UserSwitchStatusCase $userSwitchStatusCase User Switch Status Case
     *
     * @return Response
     */
    public function delete(
        int $id,
        Request $request,
        UserSwitchStatusCase $userSwitchStatusCase
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            $userSwitchStatusCase->delete($id);

            $this->addFlash('success', 'Пользователь успешно удалён!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', "Произошла ошибка в процессе изменения статуса пользователю. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }

    /**
     * Восстановление пользователя
     *
     * @Route("/restore/{id}/", methods="POST", name="restore")
     *
     * @param int $id ID пользователя
     * @param Request $request Request
     * @param UserSwitchStatusCase $userSwitchStatusCase User Switch Status Case
     *
     * @return Response
     */
    public function restore(
        int $id,
        Request $request,
        UserSwitchStatusCase $userSwitchStatusCase
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            $userSwitchStatusCase->restore($id);

            $this->addFlash('success', 'Пользователь успешно восстановлен!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', "Произошла ошибка в процессе изменения статуса пользователю. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }

    /**
     * Изменение пароля пользователю.
     *
     * @Route("/change-password/{id}/", methods="POST", name="change_password")
     *
     * @param int $id ID пользователя
     * @param Request $request Request
     * @param UserChangePasswordCase $userChangePasswordCase User ChangePassword Case
     * @param PasswordGenerateInterface $passwordGenerate Password Generate Service
     *
     * @return Response
     */
    public function changePassword(
        int $id,
        Request $request,
        UserChangePasswordCase $userChangePasswordCase,
        PasswordGenerateInterface $passwordGenerate,
    ): Response
    {
        $this->checkCsrfToken($request);

        try {
            $formData = new UserChangePasswordForm();
            $formData->id = $id;
            $formData->password = $passwordGenerate->generate();

            $userChangePasswordCase->changePassword($formData);

            $this->addFlash('success', sprintf("Пароль изменен! Новый пароль: '%s'", $formData->password));
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', "Произошла ошибка в процессе изменения пароля пользователю. Попробуйте позже.");
        }

        return $this->redirectToRoute($this->getRoute('view'), compact('id'));
    }
}
