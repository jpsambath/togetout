<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\ManagerJSON;
use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LieuController extends Controller
{
    /**
     * @Route("/ajoutLieu" , name="ajoutLieu")
     * @param ObjectManager $objectManager
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function ajoutLieu(ObjectManager $objectManager, Request $request, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            $lieuRecu = $request->getContent();
            $lieuDeserialise = $serializer->deserialize($lieuRecu, Lieu::class, 'json');
            $error = $validator->validate($lieuDeserialise);

            if (count($error) > 0) {
                throw new ErrorException("Erreur lors de la validation");
            }

            $objectManager->persist($lieuDeserialise);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['villeDeserialise'] = $lieuDeserialise;

        } catch (\Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "ajoutLieu";
        }
        return ManagerJSON::renvoiJSON($tab, $serializer);
    }
}