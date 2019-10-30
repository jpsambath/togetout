<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\ManagerJSON;
use App\Repository\LieuRepository;
use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class LieuController
 * @package App\Controller
 * @Route("/api" , name="")
 */
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
            $errors = $validator->validate($lieuDeserialise);

            if (count($errors) > 0) {
                foreach ($errors as $error){
                    $tab['messageErreur']["erreurValidation"] = $error;
                }
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
        return ManagerJSON::renvoiJSON($tab);
    }

    /**
     * * @Route("/getLieux", name="getLieux")
     * @param LieuRepository $repository
     * @return Response
     */
    public function recuperationVille(LieuRepository $repository)
    {
        try {
            $tab['statut'] = "ok";
            $tab['lieux'] = $repository->findAll();

        } catch (\Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "recuperationVille";
        }
        return ManagerJSON::renvoiJSON($tab);
    }
}
