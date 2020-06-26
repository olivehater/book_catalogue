<?php
/**
 * Error Controller
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ErrorController
 */
class ErrorController extends Controller
{
    /**
     * Access Denied action.
     *
     * @Route("/access_denied", name="access_denied")
     */
    public function accessDenied()
    {
        return $this->render('error/index.html.twig');
    }
}
