<?php

namespace App\Controller;

use App\Entity\User;
use App\Events\EmailChangedEvent;
use App\Form\Model\UserProfileModel;
use App\Form\UserProfileFormType;
use App\Security\EmailChangeVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class DashboardController extends AbstractController
{
    private EntityManagerInterface $em;
    private EmailChangeVerifier $emailVerifier;

    public function __construct(EntityManagerInterface $em, EmailChangeVerifier $emailVerifier)
    {
        $this->em = $em;
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/dashboard", name="app_dashboard")
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig',);
    }

    /**
     * @Route("/dashboard/profile", name="app_dashboard_profile")
     * @IsGranted("ROLE_USER")
     */
    public function profile(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EventDispatcherInterface    $eventDispatcher
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $userProfileModel = new UserProfileModel($user);

        $userProfileForm = $this->createForm(UserProfileFormType::class, $userProfileModel);

        $userProfileForm->handleRequest($request);

        if ($userProfileForm->isSubmitted() && $userProfileForm->isValid()) {

            if ($plainPassword = $userProfileForm->get('plainPassword')->getData()) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $plainPassword
                    )
                );

                $this->addFlash('flash_message', 'Пароль успешно изменен.');
            }

            if ($user->getFirstName() != $userProfileForm->get('firstName')->getData()) {
                $user->setFirstName($userProfileForm->get('firstName')->getData());

                $this->addFlash('flash_message', 'Имя успешно изменено.');
            }

            $this->em->persist($user);
            $this->em->flush();

            if ($user->getEmail() != $userProfileForm->get('email')->getData()) {

                $eventDispatcher->dispatch(new EmailChangedEvent($userProfileModel));

                $this->addFlash('flash_message', 'Для изменения Email перейдите по ссылке, отправленной на указанный адрес.');
            }

            return $this->redirectToRoute('app_dashboard_profile');
        }

        return $this->render('dashboard/profile.html.twig', [
            'userProfileForm' => $userProfileForm->createView()
        ]);
    }

    /**
     * @Route("/dashboard/change/email", name="app_dashboard_change_email")
     * @IsGranted("ROLE_USER")
     */
    public function changeEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('flash_message', $exception->getReason());

            return $this->redirectToRoute('app_dashboard_profile');
        }

        $this->addFlash('flash_message', 'Email успешно изменен.');

        return $this->redirectToRoute('app_dashboard_profile');
    }


    /**
     * @Route("/dashboard/profile/new/token", name="app_dashboard_new_token")
     * @IsGranted("ROLE_USER")
     */
    public function newToken(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->setToken(sha1(uniqid('token')));

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('flash_message', 'Новый токен успешно сгенерирован.');

        return $this->redirectToRoute('app_dashboard_profile');
    }
}
