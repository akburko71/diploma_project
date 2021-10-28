<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DashboardController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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
    public function profile(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $options['constraints_password'] = [
            new Length([
                'min' => 6,
                'minMessage' => 'Ваш пароль должен содержать не менее {{ limit }} символов',
                'max' => 4096,
            ]),
        ];

        $userProfileForm = $this->createForm(RegistrationFormType::class, $user, $options);

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

            $this->em->persist($user);
            $this->em->flush();

            $this->redirectToRoute('app_dashboard_profile');
        }

        return $this->render('dashboard/profile.html.twig', [
            'userProfileForm' => $userProfileForm->createView()
        ]);
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
