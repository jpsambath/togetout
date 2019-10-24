<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\SecurityAuthenticator;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param SecurityAuthenticator $authenticator
     * @param ObjectManager $objectManager
     * @param LoggerInterface $logger
     * @return Response
     */
    public function register(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder,
                             GuardAuthenticatorHandler $guardHandler, SecurityAuthenticator $authenticator, ObjectManager $objectManager,
                             LoggerInterface $logger)
    {
        try {
            if ($request->getContent() != null) {
                $logger->info($request->getContent());
                $participantRecu = $request->getContent();
                $participantRecu = $this->get('jms_serializer')->deserialize($participantRecu, 'App\Entity\Participant', 'json');
                $validator->validate($participantRecu);
            } else {
                throw new \ErrorException("Aucune valeur recue !");
            }
            /*
                       if (count($error) > 0) {
                           throw new \ErrorException("Erreur lors de la validation !");
                       }
            */

            $participantRecu->setPassword(
                $passwordEncoder->encodePassword(
                    $participantRecu,
                    $participantRecu->getPlainPassword()
                )
            );

            $logger->info($participantRecu->getPlainPassword());
            $logger->info($participantRecu->getPassword());

            $objectManager->persist($participantRecu);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['participant'] = $participantRecu;

            $logger->info($tab['statut']);

            /*return $guardHandler->authenticateUserAndHandleSuccess(
                $participantRecu,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );*/

        } catch (\Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "register";
            $tab['action2'] = "test";
            return $this->renvoiJSON($tab);
        }
    }

    private function renvoiJSON($data){
        $dataJSON = $this->get('jms_serializer')->serialize($data, 'json');

        $response = new Response($dataJSON);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
