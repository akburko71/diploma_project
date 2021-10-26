<?php

namespace App\Form\Model;

use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationFormModel
{

    /**
     * @Assert\NotBlank(message="Укажите имя")
     */
    public $firstName;

    /**
     * @Assert\NotBlank(message="Email не указан")
     * @Assert\Email(message="Введите правильный Email")
     * @UniqueUser()
     */
    public $email;

    /**
     * @Assert\NotBlank(message="Пароль не указан.")
     * @Assert\Length (min="8", minMessage="Длина пароля не менее 8-ми символов")
     */
    public $plainPassword;

}