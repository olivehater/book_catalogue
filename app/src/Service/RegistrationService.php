<?php
/**
 * Registration service.
 */

namespace App\Service;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationService
 */
class RegistrationService
{
    /**
     * Password encoder.
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * RegistrationService constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Encoding user's password.
     *
     * @param $user
     * @param $form
     *
     * @return string
     */
    public function encodingPassword($user, $form)
    {
        return $this->passwordEncoder->encodePassword(
            $user,
            $form
        );
    }
}
