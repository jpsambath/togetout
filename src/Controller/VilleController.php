<?php

namespace App\Controller;

use App\Entity\ManagerJSON;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VilleController extends Controller
{
    /**
     * @Route("/ajoutVille" , name="ajoutVille")
     * @param ObjectManager $objectManager
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function ajoutVille(ObjectManager $objectManager, Request $request, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            $villeRecu = $request->getContent();
            $villeDeserialise = $serializer->deserialize($villeRecu, Ville::class, 'json');
            $error = $validator->validate($villeDeserialise);

            if (count($error) > 0) {
                throw new ErrorException("Erreur lors de la validation");
            }

            $objectManager->persist($villeDeserialise);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['villeDeserialise'] = $villeDeserialise;

        } catch (\Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "ajoutVille";
        }
        return ManagerJSON::renvoiJSON($tab, $serializer);
    }

    /**
     * @param VilleRepository $repository
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function recuperationVille(VilleRepository $repository, Request $request, SerializerInterface $serializer)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            $tab['statut'] = "ok";
            $tab['villes'] = $repository->findAll();

        } catch (\Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "recuperationVille";
        }
        return ManagerJSON::renvoiJSON($tab, $serializer);
    }
}
