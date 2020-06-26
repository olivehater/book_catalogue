<?php
/**
 * Security controller.
 */
namespace App\Controller;

use App\Service\SecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SecurityController.
 */
class SecurityController extends AbstractController
{
    /**
     * Security service.
     *
     * @var \App\Service\SecurityService
     */
    private $securityService;

    /**
     * SecurityController constructor.
     *
     * @param SecurityService $securityService
     */
    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * @Route(
     *     "/login",
     *     name="app_login"
     * )
     * @return Response
     */
    public function login(): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('book_index');
        }
        $error = $this->securityService->lastAuthenticationError();
        $lastUsername = $this->securityService->lastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route(
     *     "/logout",
     *     name="app_logout"
     * )
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
