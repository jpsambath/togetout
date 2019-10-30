<?php

namespace App\Controller;

use App\Entity\ManagerJSON;
use App\Entity\Participant;
use App\Entity\Site;
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
 * @Route("/api", name="")
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

            $participantDeserialise = $serializer->deserialize($participantRecu, Participant::class, 'json');
            $errors = $validator->validate($participantDeserialise);

            if (count($errors) > 0) {
                $messageErreur = '';
                foreach ($errors as $error){
                    $messageErreur = $messageErreur . "\n" . $error;
                }
                throw new ErrorException($messageErreur);
            }

            $participantDeserialise->setPassword($passwordEncoder->encodePassword($participantDeserialise, $participantDeserialise->getPlainPassword()));

            $siteRepository = $objectManager->getRepository(Site::class);
            $participantDeserialise->setSite($siteRepository->find(1));

            $objectManager->persist($participantDeserialise);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Inscription réussie";

        } catch (\Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "register";
        }
        return ManagerJSON::renvoiJSON($tab);
    }

}
