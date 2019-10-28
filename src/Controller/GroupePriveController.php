<?php

namespace App\Controller;

use App\Entity\GroupePrive;
use App\Entity\ManagerJSON;
use App\Entity\Participant;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GroupePriveController extends Controller
{
    /**
     * @Route("/creerGP/{id}", name="creerGP")
     * @param Participant $participant
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     */
    public function creerGP(Participant $participant, Request $request, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            $groupePriveRecu = $request->getContent();
            $groupePriveDeserialise = $serializer->deserialize($groupePriveRecu, GroupePrive::class, 'json');
            $error = $validator->validate($groupePriveDeserialise);

            if (count($error) > 0) {
                throw new \ErrorException("Erreur lors de la validation !");
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

    }
}
