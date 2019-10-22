<?php

namespace App\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ParticipantController
 * @package App\Controller
 * @Route("/Participant", name="participant_")
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
            $participantActuel = json_decode($request->get("variable"));
            $om->persist($participantActuel);
            $om->flush();

            //$this->addFlash('success', "Votre profil a bien été modifié !");

            return $this->json($participantActuel);
        }

        throw new \ErrorException("Vous ne pouvez pas accéder à cette page !");
    }
}
