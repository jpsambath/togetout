<?php

namespace App\Controller;
use App\Entity\Sortie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
            ->findSortie(json_decode($request->get("selectSite")),
                json_decode($request->get("checkFiltreOrganisateur")),
                json_decode($request->get("checkFiltreInscrit")),
                json_decode($request->get("checkFiltrePasInscrit")),
                json_decode($request->get("checkFiltreSortiePasse")),
                json_decode($request->get("filtreSaisieNom")),
                json_decode($request->get("idParticipant")),
                json_decode($request->get("filtreDateDebut")),
                json_decode($request->get("filtreDateFin")));
        return new JsonResponse(json_encode(['listeSortie' => $listeSortie]));
    }
}
