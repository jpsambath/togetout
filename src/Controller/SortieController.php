<?php

namespace App\Controller;

use App\Entity\Sortie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends Controller
{
    /**
     * @Route("/sortie", name="sortie")
     */
    public function index()
    {
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }

    /**
     * @Route("/inscriptionSortie/{id}", name="inscriptionSortie")
     * @param Sortie $sortie
     * @return void
     */
    public function inscriptionSortie(Sortie $sortie)
    {
        $sortie->inscrirePArticipant($this->getUser());

    }

    /**
     * @Route("/desistementSortie/{id}", name="desistementSortie")
     * @param Sortie $sortie
     * @return void
     */
    public function desistementSortie(Sortie $sortie)
    {
        $sortie->desinscrirePArticipant($this->getUser());

    }

    /**
     * @Route("/listeSortiesParSite", name="listeSortiesParSite")
     * @param Request $request
     * @return JsonResponse
     **/
    public function listeSortiesParSite(Request $request)
    {
        $listeSortie = $this->getDoctrine()
            ->getManager()
            ->getRepository('App\Entity\Sortie')
            ->findSortieBySite(json_decode($request->get("selectSite")),json_decode($request->get("checkFiltreOrganisateur")),json_decode($request->get("checkFiltreInscrit")),json_decode($request->get("checkFiltrePasInscrit")),json_decode($request->get("checkFiltreSortiePasse")),json_decode($request->get("filtreDateDebut")),json_decode($request->get("filtreDateFin")),json_decode($request->get("filtreSaisieNom")));
        return new JsonResponse(json_encode(['listeSortie' => $listeSortie]));
    }

    /**
     * findSortie(
     *  json_decode($request->get("site")),
     * json_decode($request->get("checkFiltreOrganisateur")),
     * json_decode($request->get("checkFiltreInscrit")),
     * json_decode($request->get("checkFiltrePasInscrit")),
     * json_decode($request->get("checkFiltreSortiePasse")),
     * json_decode($request->get("filtreDateDebut")),
     * json_decode($request->get("filtreDateFin")),
     * json_decode($request->get("filtreSaisieNom")));
     *
     */



    //,json_decode($request->get("checkFiltreOrganisateur")),json_decode($request->get("checkFiltreInscrit")),json_decode($request->get("checkFiltrePasInscrit")),json_decode($request->get("checkFiltreSortiePasse")),json_decode($request->get("filtreDateDebut")),json_decode($request->get("filtreDateFin")),json_decode($request->get("filtreSaisieNom"))

}
