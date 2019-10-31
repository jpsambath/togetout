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
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
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
     * @return Response
     */
    public function ajoutLieu(ObjectManager $objectManager, Request $request, ValidatorInterface $validator)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            $lieuRecu = $request->getContent();

            $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
            $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [new JsonEncoder()]);

            $lieuDeserialise = $serializer->deserialize($lieuRecu, Lieu::class, 'json');
            $errors = $validator->validate($lieuDeserialise);

            if (count($errors) > 0) {
                $messageErreur = '';
                foreach ($errors as $error){
                    $messageErreur = $messageErreur . "\n" . $error;
                }
                throw new ErrorException($messageErreur);
            }

            $ville = $objectManager->merge($lieuDeserialise->getVille());
            $lieuDeserialise->setVille($ville);

            $objectManager->persist($lieuDeserialise);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Lieu creer avec success !";

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
