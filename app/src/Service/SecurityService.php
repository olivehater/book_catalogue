<?php
/**
 * Security service.
 */

namespace App\Service;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityService.
 */
class SecurityService
{
    /**
     * Authentication utils.
     *
     * @var \Symfony\Component\Security\Http\Authentication\AuthenticationUtils
     */
    private $authenticationUtils;

    /**
     * SecurityService constructor.
     */
    public function __construct(AuthenticationUtils $authenticationUtils)
    {
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * Get last authentication error.
     *
     * @return \Symfony\Component\Security\Core\Exception\AuthenticationException|null
     */
    public function lastAuthenticationError()
    {
        return $this->authenticationUtils->getLastAuthenticationError();
    }

    /**
     * Get last username.
     *
     * @return string
     */
    public function lastUsername()
    {
        return $this->authenticationUtils->getLastUsername();
    }
}
