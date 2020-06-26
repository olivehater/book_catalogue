<?php
/**
 * Access denied handler.
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

/**
 * Class AccessDeniedHandler.
 */
class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $router;

    /**
     * AccessDeniedHandler constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Access Denied Handler.
     *
     * @param Request               $request
     * @param AccessDeniedException $accessDeniedException
     *
     * @return RedirectResponse
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $url = $this->router->generate('access_denied');

        return new RedirectResponse($url);
    }
}
