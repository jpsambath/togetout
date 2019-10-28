<?php

namespace App\Controller;


use App\Entity\Participant;
use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DavidController extends Controller
{
//INSERTION D'UN LIEU ???????
    /**
     * @Route("/recuperationJsonSortie" , name="recuperationJasonSortie")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @throws ErrorException
     */
    public function recuperationSortieJson(Request $request, ValidatorInterface $validator)
    {
        try {
            $sortieRecu = $request->getContent();
            $sortieRecu = $this->get('jms_serializer')->deserialize($sortieRecu, 'AppBundle\Entity\Lieu', 'json');
            $error = $validator->validate($sortieRecu);

            if (count($error) > 0) {
                throw new ErrorException("Erreur lors de la validation");
            }
            $tab['statut'] = "ok";
            $tab['lieuRecu'] = $sortieRecu;
        } catch (Exception $e) {

            $tab['statut'] = 'erreur';
            $tab['lieuNonRecu'] = 'le lieu n\'à pas été transmit correctement';
        }
        return $this->json("Donné recupéré !");
    }

//????????????????
    /**
     * @Route("/supprimerUtilisateur", name="supprimerUtilisateur")
     * @param Participant $participant
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param ObjectManager $objectManager
     * @return JsonResponse
     * @throws ErrorException
     */
    public function supprimerUtilisateur(Participant $participant, Request $request, ValidatorInterface $validator, ObjectManager $objectManager)
    {
        if ($request->getContent() != null) {
            $listeChangementEtat = $request->getContent();
            $listeChangementEtat = $this->get('jms_serializer')->deserialize($listeChangementEtat, 'App\Entity\Participant', 'json');
            $error = $validator->validate($listeChangementEtat);
        } else {
            throw new ErrorException("Aucune valeur recue !");
        }

        if ($participant->isAdministrateur() && $listeChangementEtat === null) {
            foreach ($listeChangementEtat as $participant) {
                $objectManager->remove($participant);
                $objectManager->flush();
            }
        }
        return $this->json("Utilisateur supprimé !");
    }


    //??????????
    /**
     * @Route("/inscrireUtilisateur", name="sinscrireUtilisateur")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param Participant $participant
     * @param ObjectManager $objectManager
     * @return JsonResponse
     * @throws ErrorException
     */
    public function inscrireManuellementUtilisateur (Request $request, ValidatorInterface $validator, Participant $participant, ObjectManager $objectManager)
    {
        if ($request->getContent() != null) {
            $newUtilisateur = $request->getContent();
            $newUtilisateur = $this->get('jms_serializer')->deserialize($newUtilisateur, 'App\Entity\Participant', 'json');
            $error = $validator->validate($newUtilisateur);
        } else {
            throw new ErrorException("Aucune valeur recue !");
        }

        if ($participant->isAdministrateur() === true && $newUtilisateur === null)
        {

            $objectManager->persist($newUtilisateur);
            $objectManager->flush();
        }
        return $this->json("utilisateur inscrit!");
    }

}


