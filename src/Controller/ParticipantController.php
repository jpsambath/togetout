<?php

namespace App\Controller;

use App\Entity\Participant;
use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ParticipantController
 * @package App\Controller
 * @Route("/api/participant", name="participant_")
 */
class ParticipantController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    /**
     * @Route("/modifier", name="modifier")
     * @param Request $request
     * @param ObjectManager $om
     * @return JsonResponse
     * @throws ErrorException
     */
    public function modifierProfil(Request $request, ObjectManager $om){
        if ($request->isXmlHttpRequest()){
            $nouveauParticipant = json_decode($request->get("nouveauParticipant"));
            $om->persist($nouveauParticipant);
            $om->flush();

            //$this->addFlash('success', "Votre profil a bien été modifié !");

            return $this->json($nouveauParticipant);
        }

        throw new \ErrorException("Vous ne pouvez pas accéder à cette page !");
    }

    /**
     * @return JsonResponse
     * @Route("/test", name="test")
     */
    public function test(){
        $test = new Participant();

        $test->setNom('ROY');
        $test->setPrenom('Loïc');
        $test->setEmail('loic.roy2019@campus-eni.fr');
        $test->setUsername('username');
        $test->setPassword('123456789/Test');

        return $this->json($test);
    }
}
