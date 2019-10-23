<?php

namespace App\Controller;

use App\Entity\Sortie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class DavidController extends Controller
{
    /**
     * @Route("/david", name="david")
     */
    public function index()
    {
        return $this->render('david/index.html.twig', [
            'controller_name' => 'DavidController',
        ]);
    }




    /**
     * @Route("/clotureincription/{id}", name="clotureInscription")
     * @param Sortie $sortie
     * @throws \Exception
     */
    public function clotureInscrition(Sortie $sortie)
    {
        if ($this->getDateLimiteInscription() <= (new \DateTime()))
        {
            $sortie->inscriptionCloturee();
        }
    }

    /**
     * @Route("/annulerSorite/{id}", name="dannulertSortie")
     * @param Sortie $sortie
     */
    public function annulerSortie(Sortie $sortie)
    {
        $squery = $this->createQueryBuilder('s');

    }
}
