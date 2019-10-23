<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\User;
use App\Security\SecurityAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * Class RegistrationController
 * @package App\Controller
 * @Route("/api")
 */
class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param SecurityAuthenticator $authenticator
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, SecurityAuthenticator $authenticator): Response
    {

        /*
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
        */

        $test = new Participant();
        $test2 = new Participant();

        $test->setNom('ROY');
        $test->setPrenom('Loïc');
        $test->setEmail('loic.roy2019@campus-eni.fr');
        $test->setUsername('username');
        $test->setPassword('123456789/Test');

        $test2->setNom('ROY2');
        $test2->setPrenom('Loïc2');
        $test2->setEmail('loic.roy2019@campus-eni.fr2');
        $test2->setUsername('username2');
        $test2->setPassword('123456789/Test2');

        $tab['participants'][] = $test;
        $tab['participants'][] = $test2;


        return $this->json($tab);
    }
}
