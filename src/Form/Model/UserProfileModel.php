<?php

namespace App\Form\Model;

use App\Entity\User;

class UserProfileModel
{
    private $firstName;

    private $email;

    public function __construct(User $user)
    {
        $this->firstName = $user->getFirstName();
        $this->email = $user->getEmail();
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string|null $firstName
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

}