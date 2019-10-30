<?php

namespace App\Controller;


use App\Entity\Lieu;
use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DavidController extends Controller
{

    /**
     * @Route("/recuperationJsonSortie" , name="recuperationJasonSortie")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param ObjectManager $objectManager
     * @return JsonResponse
     */
    public function recuperationSortieJson(Request $request, ValidatorInterface $validator, ObjectManager $objectManager)
    {
        try {
            $lieuRecu = $request->getContent();
            $lieuDeserialise = $this->get('jms_serializer')->deserialize($lieuRecu, Lieu::class, 'json');
            $error = $validator->validate($lieuDeserialise);

            if (count($error) > 0) {
                throw new ErrorException("Erreur lors de la validation");
            }

            $objectManager->persist($lieuDeserialise);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Lieu creer avec success !";
            $tab['lieuRecu'] = $lieuDeserialise;
        } catch (Exception $e) {

            $tab['statut'] = 'erreur';
            $tab['lieuNonRecu'] = 'le lieu n\'à pas été transmit correctement';
        }
        return $this->json("Donné recupéré !");
    }




}


