<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Tests\Util\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/annulerSorite/{id}", name="annulertSortie")
     * @param Sortie $sortieAnnule
     * @param ObjectManager $objectManager
     * @return JsonResponse
     */
    public function annulationSortie(Sortie $sortieAnnule, ObjectManager $objectManager, Participant $participant)
    {
        if($participant->)
        {
            $objectManager->remove($sortieAnnule);
            $objectManager->flush();

        }


        return $this->json("la suppression de la sortie a été prise en compte !");
    }

    /**
     * @Route("/consulterProfil/{id}", name="consulterProfil")
     * @param Participant $participantRecupEnBDD
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    public function consulterProfil(Participant $participantRecupEnBDD, UserRepository $userRepository)
    {

        return $this->json($participantRecupEnBDD);

    }

    /**
     * @Route("/ajoutLieu" , name="ajoutLieu")
     * @param ObjectManager $objectManager
     * @return JsonResponse
     */
    public function  ajoutLieu(ObjectManager $objectManager)
    {
        $ajoutLieuEnBDD = new Lieu();
        $objectManager->persist($ajoutLieuEnBDD);
        $objectManager->flush();

        return $this->json($ajoutLieuEnBDD);
    }

    /**
     * @Route("/ajoutVille" , name="ajoutVille")
     * @param ObjectManager $objectManager
     * @return JsonResponse
     * @throws \ErrorException
     */
    public function ajoutVille(ObjectManager $objectManager)
    {
        $ajoutVilleEnBdd = new Ville();

        if ($ajoutVilleEnBdd->getCodePostal() !== null)
        {
            $objectManager->persist($ajoutVilleEnBdd);
            $objectManager->flush();
        } else {
            throw new \ErrorException('une ville ne peut éxister sans code postal !');
        }

        return $this->json($ajoutVilleEnBdd);
    }


    /**
     * @Route("/recuperationJasonLieu" , name="recuperationJasonLieu")
     * @param Request $request
     * @param Validator $validator
     * @throws \ErrorException
     */
    public  function recuperationLieuJson(Request $request,Validator $validator)
    {
        try{
            $lieuRecu = $request->getContent();
            $lieuRecu = $this->get('jms_serializer')->deserialize($lieuRecu, 'AppBundle\Entity\Lieu', 'json');
            $error = $validator->validate($lieuRecu);

            if(count($error) > 0) {
                throw new \ErrorException("Erreur lors de la validation");
            }
            $tab['statut'] = "ok";
            $tab['lieuRecu'] = $lieuRecu;
        } catch{

            $tab['statut'] = 'erreur';
            $tab['lieuNonRecu'] = 'le lieu n\'à pas été transmit correctement';
        }
    }


}
