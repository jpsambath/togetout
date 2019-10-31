<?php

namespace App\Controller;


use App\Entity\ManagerJSON;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use JMS\Serializer\SerializerInterface;
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
 * Class VilleController
 * @package App\Controller
 *  * @Route("/api" , name="")
 */
class VilleController extends Controller
{
    /**
     * @Route("/ajoutVille" , name="ajoutVille")
     * @param ObjectManager $objectManager
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function ajoutVille(ObjectManager $objectManager, Request $request, ValidatorInterface $validator)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            $villeRecu = $request->getContent();

            $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
            $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [new JsonEncoder()]);

            $villeDeserialise = $serializer->deserialize($villeRecu, Ville::class, 'json');
            $errors = $validator->validate($villeDeserialise);

            if (count($errors) > 0) {
                $messageErreur = '';
                foreach ($errors as $error){
                    $messageErreur = $messageErreur . "\n" . $error;
                }
                throw new ErrorException($messageErreur);
            }

            $objectManager->persist($villeDeserialise);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Ville créée avec succes !";

        } catch (\Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "ajoutVille";
        }
        return ManagerJSON::renvoiJSON($tab);
    }

    /**
     * @Route("/getVilles", name="getVilles")
     * @param VilleRepository $repository
     * @return Response
     */
    public function recuperationVille(VilleRepository $repository)
    {
        try {
            $tab['statut'] = "ok";
            $tab['villes'] = $repository->findAll();

        } catch (\Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "recuperationVille";
        }
        return ManagerJSON::renvoiJSON($tab);
    }
}
