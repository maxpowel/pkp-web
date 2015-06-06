<?php

namespace PKP\Bundle\AuthBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class UserRegister
{
    /**
     * @Assert\NotBlank()
     */
    public $username;

    /**
     * @Assert\NotBlank()
     */
    public $lastname;

    /**
     * @Assert\NotBlank()
     */
    public $firstname;

    /**
     * @Assert\NotBlank()
     */
    public $password;

    /**
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     */
    public $email;
}
