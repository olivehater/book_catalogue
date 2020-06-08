<?php
/**
 * Access denied handler.
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

/**
 * Class AccessDeniedHandler.
 */
class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /**
     * Access Denied Handler.
     *
     * @param Request $request
     * @param AccessDeniedException $accessDeniedException
     *
     * @return Response|null
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $content = 'Access denied';

        return new Response($content, 403);
    }
}
