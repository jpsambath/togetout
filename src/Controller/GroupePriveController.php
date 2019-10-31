<?php

namespace App\Controller;

use App\Entity\GroupePrive;
use App\Entity\ManagerJSON;
use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class GroupePriveController
 * @package App\Controller
 * @Route("/api", name="")
 */
class GroupePriveController extends Controller
{
    /**
     * @Route("/creerGP/{id}", name="creerGP", requirements={"id": "\d+"})
     * @param Participant $participant
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function creerGP(Participant $participant, Request $request, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            $groupePriveRecu = $request->getContent();
            $groupePriveDeserialise = $serializer->deserialize($groupePriveRecu, GroupePrive::class, 'json');
            $errors = $validator->validate($groupePriveDeserialise);

            if (count($errors) > 0) {
                $messageErreur = '';
                foreach ($errors as $error){
                    $messageErreur = $messageErreur . "\n" . $error;
                }
                throw new ErrorException($messageErreur);
            }

            $participant->addGroupePrivesFondateur($groupePriveDeserialise);

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Inscription successfull";

        } catch (\Exception $e){
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "creerGP";
        }
        return ManagerJSON::renvoiJSON($tab);
    }
}
