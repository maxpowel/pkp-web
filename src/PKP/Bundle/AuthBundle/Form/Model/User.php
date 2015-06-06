<?php

namespace PKP\Bundle\AuthBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class User
{
    public $username;

    public $lastname;

    public $firstname;

    public $password;

    /**
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    public $email;
}
