<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\SubscriptionRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixtures
{
    private UserPasswordHasherInterface $passwordHasher;
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        SubscriptionRepository $subscriptionRepository
    )
    {
        $this->passwordHasher = $passwordHasher;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function loadData(ObjectManager $manager)
    {
        $subscription = $this->subscriptionRepository->findOneBy(["cost" => 0]);

        $this->create(User::class, function (User $user) use ($manager, $subscription) {
            $user
                ->setFirstName('Александр')
                ->setEmail('akburko@yandex.ru')
                ->setPassword($this->passwordHasher->hashPassword($user, '611651'))
                ->setRoles(["ROLE_ADMIN"])
                ->setSubscription($subscription)
                ->setIsVerified(true)
            ;

        });

        $this->create(User::class, function (User $user) use ($manager, $subscription) {
            $user
                ->setFirstName('Администратор')
                ->setEmail('admin@hlite.ru')
                ->setPassword($this->passwordHasher->hashPassword($user, '123456'))
                ->setRoles(["ROLE_USER"])
                ->setSubscription($subscription)
                ->setIsVerified(true)
            ;

        });

        $this->manager->flush();
    }

    public function getDependencies()
    {
        return [
            SubscriptionFixtures::class,
        ];
    }
}
