<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class LoicController
 * @package App\Controller
 * @Route("/api/test")
 */
class LoicController extends Controller
{
    /**
     * @Route("/loic", name="loic")
     */
    public function index()
    {
        return $this->render('loic/index.html.twig');
    }


    /**
     * @Route("/responseJSON")
     * @param Request $request
     * @param LoggerInterface $logger
     * @param ValidatorInterface $validator
     * @param ObjectManager $objectManager
     * @return Response
     */
    public function sendJSON(Request $request, LoggerInterface $logger, ValidatorInterface $validator, ObjectManager $objectManager)
    {
        try {
            if ($request->getContent() != null) {
                $participantRecu = $request->getContent();
                $participantRecu = $this->get('jms_serializer')->deserialize($participantRecu, 'App\Entity\Participant', 'json');
                $error = $validator->validate($participantRecu);
            } else {
                throw new \ErrorException("Aucune valeur recue !");
            }

            if (count($error) > 0) {
                throw new \ErrorException("Erreur lors de la validation !");
            }

            $objectManager->persist($participantRecu);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['participant'] = $participantRecu;


        } catch (\Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            return $this->renvoiJSON($tab);
        }
    }

   private function renvoiJSON($data){
       $dataJSON = $this->get('jms_serializer')->serialize($data, 'json');

       $response = new Response($dataJSON);
       $response->headers->set('Content-Type', 'application/json');

       return $response;
   }

}