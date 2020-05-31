<?php
/**
 * Registration controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserData;
use App\Form\RegistrationType;
use App\Repository\UserDataRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationController.
 */
class RegistrationController extends AbstractController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request                             $request         HTTP request
     * @param \App\Repository\UserRepository                                        $userRepository  User repository
     * @param \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface $passwordEncoder Password encoder
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/registration",
     *     name="register"
     * )
     */
    public function register(Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, UserDataRepository $userDataRepository): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('book_index');
        }

        $user = new User();
        $userData = new UserData();
        $form = $this->createForm(RegistrationType::class, $userData);
        $form->handleRequest($request);

        // zakodowane hasło
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('user')->get('password')->getData()
                )
            );
            $user->setEmail($form->get('user')->get('email')->getData());
            $user->setUserData($userData);
            $user->setRoles(['ROLE_USER']);
            $user->setRoles(['ROLE_USER']);
            $userDataRepository->save($userData);
            $userRepository->save($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->persist($userData);
            $entityManager->flush();

            $this->addFlash('success', 'message_registered_successfully');

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'registration/register.html.twig',
            ['form' => $form->createView()]
        );
    }
}
