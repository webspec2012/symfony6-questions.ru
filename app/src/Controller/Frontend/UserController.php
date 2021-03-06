<?php
namespace App\Controller\Frontend;

use App\Core\Exception\AppException;
use App\Core\Exception\ServiceException;
use App\Core\Security\FrontendLoginFormAuthenticator;
use App\Users\Dto\User\UserRegistrationForm;
use App\Users\Form\User\UserRegistrationFormType;
use App\Users\Form\User\UserRestorePasswordRequestFormType;
use App\Users\UseCase\User\UserEmailVerificationCase;
use App\Users\UseCase\User\UserFindCase;
use App\Users\UseCase\User\UserPasswordRestoreCase;
use App\Users\UseCase\User\UserRegistrationCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

/**
 * Контроллер для работы с пользователями
 *
 * @psalm-suppress PropertyNotSetInConstructor
 *
 * @Route("/user", name="user_")
 */
final class UserController extends AppController
{
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
     * Регистрация пользователя.
     *
     * @Route("/registration/", name="registration")
     *
     * @param Request $request Request
     * @param FrontendLoginFormAuthenticator $loginFormAuthenticator Login Form Authenticator
     * @param UserAuthenticatorInterface $userAuthenticator User Authenticator
     * @param RateLimiterFactory $userRegistrationLimiter Rate Limiter
     * @param UserRegistrationCase $userRegistrationCase User Registration Case
     *
     * @return Response Response
     */
    public function registration(
        Request $request,
        FrontendLoginFormAuthenticator $loginFormAuthenticator,
        UserAuthenticatorInterface $userAuthenticator,
        RateLimiterFactory $userRegistrationLimiter,
        UserRegistrationCase $userRegistrationCase,
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToUserProfile();
        }

        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Rate Limiter
                $limiter = $userRegistrationLimiter->create($request->getClientIp());
                if (false === $limiter->consume()->isAccepted()) {
                    throw new ServiceException("Превышен лимит на количество регистраций. Попробуйте позже.");
                }

                // Регистрация
                $formData = $this->formLoadData($form->getData(), UserRegistrationForm::class);
                $user = $userRegistrationCase->registration($formData);

                // Авторизация
                $userAuthenticator->authenticateUser($user, $loginFormAuthenticator, $request);
                $this->addFlash('success',"Вы успешно зарегистрированы на сайте!");

                return $this->redirectToUserProfile();
            } catch (AppException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                $this->logger->error(__METHOD__.': '.$e->getMessage());
                $this->addFlash('error', "Произошла ошибка в процессе регистрации. Попробуйте позже.");
            }
        }

        return $this->render('user/registration', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Авторизация пользователя.
     *
     * @Route("/login/", name="login")
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToUserProfile();
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Выход пользователя.
     *
     * @Route("/logout/", name="logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Запрос на восстановление пароля пользователя
     *
     * @Route("/password-restore/request/", name="password_restore_request")
     *
     * @param Request $request Request
     * @param RateLimiterFactory $userPasswordRestoreLimiter Rate Limiter
     * @param UserFindCase $userFindCase User Find Case
     * @param UserPasswordRestoreCase $userPasswordRestoreCase User Password Restore Case
     *
     * @return Response Response
     */
    public function passwordRestoreRequest(
        Request $request,
        RateLimiterFactory $userPasswordRestoreLimiter,
        UserFindCase $userFindCase,
        UserPasswordRestoreCase $userPasswordRestoreCase,
    ): Response
    {
        $form = $this->createForm(UserRestorePasswordRequestFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Rate Limiter
                $limiter = $userPasswordRestoreLimiter->create($request->getClientIp());
                if (false === $limiter->consume()->isAccepted()) {
                    throw new ServiceException("Превышен лимит на количество запросов восстановления пароля. Попробуйте позже.");
                }

                $user = $userFindCase->getUserByEmail((string) ($form->getData()['email'] ?? ''));
                if (!$userPasswordRestoreCase->sendEmail($user->getId())) {
                    throw new ServiceException("Ошибка при запросе восстановления пароля. Попробуйте позже.");
                }

                $this->addFlash('success',"Мы отправили Вам на почту письмо с подтверждением смены пароля. Перейдите по ссылке из письма.");

                return $this->redirectToAuthbox();
            } catch (AppException $e) {
                $this->addFlash('error', $e->getMessage());
            } catch (\Throwable $e) {
                $this->logger->error(__METHOD__.': '.$e->getMessage());
                $this->addFlash('error', "Произошла ошибка в процессе восстановления пароля. Попробуйте позже.");
            }
        }

        return $this->render('user/password-restore-request', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Изменение пароля пользователю на основе токена восстановления пароля.
     *
     * @Route("/password-restore/reset/", name="password_restore_reset")
     *
     * @param Request $request Request
     * @param UserPasswordRestoreCase $userPasswordRestoreCase User Password Restore Case
     *
     * @return Response Response
     */
    public function passwordRestoreReset(
        Request $request,
        UserPasswordRestoreCase $userPasswordRestoreCase,
    ): Response
    {
        try {
            if (!$userPasswordRestoreCase->handle((string) $request->query->get('token'))) {
                throw new ServiceException("Ошибка при сбросе пароля. Попробуйте позже.");
            }

            $this->addFlash('success', 'Пароль успешно изменен! Новый пароль отправлен вам на почту.');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', "Произошла ошибка при восстановлении пароля. Попробуйте позже.");
        }

        return $this->redirectToAuthbox();
    }

    /**
     * Подтверждение E-mail адреса пользователя
     *
     * @Route("/email-verification/", name="email_verification")
     *
     * @param Request $request Request
     * @param UserEmailVerificationCase $userEmailVerificationCase User Email Verification Case
     *
     * @return Response Response
     */
    public function emailVerification(
        Request $request,
        UserEmailVerificationCase $userEmailVerificationCase,
    ): Response
    {
        try {
            if (!$userEmailVerificationCase->handle((string) $request->query->get('token'))) {
                throw new ServiceException("Ошибка при подтверждении e-mail адреса. Попробуйте позже.");
            }

            $this->addFlash('success', 'Ваш E-mail успешно подтвержден!');
        } catch (AppException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', "Произошла ошибка при подтверждении E-mail адреса. Попробуйте позже.");
        }

        return $this->redirectToAuthbox();
    }
}
