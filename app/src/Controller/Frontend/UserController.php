<?php
namespace App\Controller\Frontend;

use App\Core\Exception\AppException;
use App\Core\Security\FrontendLoginFormAuthenticator;
use App\Users\Form\User\UserRegistrationFormType;
use App\Users\UseCase\User\UserRegistrationCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

/**
 * Контроллер для работы с пользователями
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
     * @param FrontendLoginFormAuthenticator $loginFormAuthenticator
     * @param UserAuthenticatorInterface $userAuthenticator
     *
     * @param UserRegistrationCase $userRegistrationCase User Registration Case
     *
     * @return Response Response
     */
    public function create(
        Request $request,
        FrontendLoginFormAuthenticator $loginFormAuthenticator,
        UserAuthenticatorInterface $userAuthenticator,

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
                // Регистрация
                $user = $userRegistrationCase->registration($form->getData());

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
     * @return Response
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
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Запрос на восстановление пароля пользователя
     *
     * @Route("/password-restore/request/", name="password_restore_request")
     *
     * @return Response Response
     */
    public function passwordRestoreRequest(): Response
    {
        // @TODO
        return $this->render('user/password-restore-request');
    }
}
