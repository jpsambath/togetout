<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/responseJSON/{id}")
     * @param Request $request
     * @return Response
     */
    public function sendJSON(Request $request)
    {
        $data = $request->get("participant");
        $participantRecu = $this->get('jms_serializer')->deserialize($data, 'AppBundle\Entity\Article', 'json');
        //$this->get('validator')->validate($participant);

        $participant = new Participant();
        $participant2 = new Participant();

        $participant->setNom('ROY');
        $participant->setPrenom('Loïc');
        $participant->setEmail('loic.roy2019@campus-eni.fr');
        $participant->setUsername('username');
        $participant->setPassword('123456789/Test');

        $participant2->setNom('ROY2');
        $participant2->setPrenom('Loïc2');
        $participant2->setEmail('loic.roy2019@campus-eni.fr2');
        $participant2->setUsername('username2');
        $participant2->setPassword('123456789/Test2');
        $participant2->setSite(new Site());

        $tab['statut'] = "ok";
        $tab["objetRecu"] = $participantRecu;
        $tab['test'] = "test";
        $tab['participants'][] = $participant;
        $tab['participants'][] = $participant2;

        $data = $this->get('jms_serializer')->serialize($tab, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');

        //var_dump($response);

        return $response;
    }

    /**
     * @Route("/requestJSON", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function getJSON(Request $request)
    {
        $data = $request->getContent();
        $participant = $this->get('jms_serializer')->deserialize($data, 'AppBundle\Entity\Article', 'json');
        $this->get('validator')->validate($participant);

        var_dump($participant);

        return new Response('', Response::HTTP_CREATED);
    }

}