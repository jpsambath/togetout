<?php

namespace App\Controller;

use App\Entity\Participant;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
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
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     * @param Serializer $serializer
     * @return Response
     */
    public function sendJSON(Request $request, ValidatorInterface $validator, LoggerInterface $logger, SerializerInterface $serializer)
    {
        $logger->info("T'arrive sur l'API");
        $tab = [];
        if ($request->getContent() != null) {
            $participantRecu = $request->getContent();

            $tab["participantRecu"] = $participantRecu;

            //version PLUG IN
            //$participantDeserialiser = $this->get('jms_serializer')->deserialize($participantRecu, Participant::class, 'json');

            //version NATIF
            $participantDeserialiser = $serializer->deserialize($participantRecu, Participant::class, 'json');

            //$validator->validate($participantDeserialiser);

            $tab["participantDeserialiser"] = $participantDeserialiser;

            //version NATIF
            return $serializer->serialize($tab, 'json');
        }

        //version PLUG IN
        //return $this->renvoiJSON($tab, $logger);

    }

   private function renvoiJSON($data, $logger){
       $dataJSON = $this->get('jms_serializer')->serialize($data, 'json');

       $logger->info($dataJSON);

       $response = new Response($dataJSON);
       $response->headers->set('Content-Type', 'application/json');

       $logger->info("Tu repars de l'API");

       return $response;
   }

}