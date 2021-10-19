<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixtures
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function loadData(ObjectManager $manager)
    {
        $this->create(User::class, function (User $user) use ($manager) {
            $user
                ->setEmail('akburko@yandex.ru')
                ->setPassword($this->passwordHasher->hashPassword($user, '123456'))
                ->setRoles(["ROLE_ADMIN"])
            ;

        });

        $this->create(User::class, function (User $user) use ($manager) {
            $user
                ->setEmail('admin@hlite.ru')
                ->setPassword($this->passwordHasher->hashPassword($user, '123456'))
                ->setRoles(["ROLE_USER"])
            ;

        });

        $this->manager->flush();
    }
}
