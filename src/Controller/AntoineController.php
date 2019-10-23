<?php

namespace App\Controller;
use App\Entity\Sortie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AntoineController extends Controller
{
    /**
     * @Route("/antoine", name="antoine")
     */
    public function index()
    {
        return $this->render('antoine/index.html.twig', [
            'controller_name' => 'AntoineController',
        ]);
    }

    /**
     * @Route("/listeSorties", name="listeSorties")
     * @param Request $request
     * @return JsonResponse
     **/
    public function listeSorties(Request $request)
    {
        $listeSortie = $this->getDoctrine()
            ->getManager()
            ->getRepository('App\Entity\Sortie')
            ->findSortie(json_decode($request->get("selectSite")), //Id du site selectionné
                json_decode($request->get("checkFiltreOrganisateur")), //Boolean
                json_decode($request->get("checkFiltreInscrit")), //Boolean
                json_decode($request->get("checkFiltrePasInscrit")), //Boolean
                json_decode($request->get("checkFiltreSortiePasse")), //Boolean
                json_decode($request->get("filtreSaisieNom")), //Valeur de l'input
                json_decode($request->get("idParticipant")), //Id de l'utilisateur connecté
                json_decode($request->get("filtreDateDebut")), //valeur de l'input de type date du premier intervalle
                json_decode($request->get("filtreDateFin"))); //valeur de l'input de type date du deuxième intervalle
        return new JsonResponse(json_encode(['listeSortie' => $listeSortie]));
    }

    /**
     * @Route("/creerSortie", name="creerSortie")
     * @param Request $request
     * @return JsonResponse
     **/
    public function creerSortie(Request $request)
    {

    }
}
