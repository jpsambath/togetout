<?php

namespace App\Controller;

use App\Entity\ManagerJSON;
use App\Entity\Participant;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
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
     * @param ObjectManager $objectManager
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function register(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder,
                             ObjectManager $objectManager, SerializerInterface $serializer)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            $participantRecu = $request->getContent();

            $participantRecu = $serializer->deserialize($participantRecu, Participant::class, 'json');
            $error = $validator->validate($participantRecu);

            if (count($error) > 0) {
                throw new \ErrorException("Erreur lors de la validation !");
            }

            $participantRecu->setPassword($passwordEncoder->encodePassword($participantRecu, $participantRecu->getPlainPassword()));

            $objectManager->persist($participantRecu);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Inscription réussie";


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
        }

        return ManagerJSON::renvoiJSON($tab, $serializer);
    }

}
