<?php

namespace App\Events;

use App\Form\Model\UserProfileModel;
use Symfony\Contracts\EventDispatcher\Event;

class EmailChangedEvent extends Event
{
    private UserProfileModel $user;

    public function __construct(UserProfileModel $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}