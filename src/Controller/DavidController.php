<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @param Participant $participant
     * @return JsonResponse
     * @throws \ErrorException
     */
    public function annulationSortie(Sortie $sortieAnnule, ObjectManager $objectManager, Participant $participant)
    {
        if($participant->getId() === $sortieAnnule->getOrganisateur()->getId() || $participant->isAdministrateur()=== true)
        {
            $objectManager->remove($sortieAnnule);
            $objectManager->flush();

        }else{
            throw new \ErrorException('Vous n\'avez pas les droits!');
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
     *  @Route("/recuperationJasonsortie" , name="recuperationJasonSortie")
     * @param Request $request
     * @param ValidatorInterface $validator
     */
    public  function recuperationSortieJson(Request $request,ValidatorInterface $validator)
    {
        try{
            $sortieRecu = $request->getContent();
            $sortieRecu = $this->get('jms_serializer')->deserialize($sortieRecu, 'AppBundle\Entity\Lieu', 'json');
            $error = $validator->validate($sortieRecu);

            if(count($error) > 0) {
                throw new \ErrorException("Erreur lors de la validation");
            }
            $tab['statut'] = "ok";
            $tab['lieuRecu'] = $sortieRecu;
        } catch(\Exception $e) {

            $tab['statut'] = 'erreur';
            $tab['lieuNonRecu'] = 'le lieu n\'à pas été transmit correctement';
        }
    }

    private function renvoiSortieJSON($data){
        $dataJSON = $this->get('jms_serializer')->serialize($data, 'json');

        $response = new Response($dataJSON);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    public function desactiverUtilisateur(Participant $participant, $request, $validator, $listeChangementEtat, $objectManager)
    {
        if ($request->getContent() != null) {
            $listeChangementEtat = $request->getContent();
            $listeChangementEtat = $this->get('jms_serializer')->deserialize($listeChangementEtat, 'App\Entity\Participant', 'json');
            $error = $validator->validate($listeChangementEtat);
        } else {
            throw new \ErrorException("Aucune valeur recue !");
        }

        if($participant->isAdministrateur()=== true && $listeChangementEtat === null){
            foreach ($listeChangementEtat as $participant){
                $objectManager->persist($participant);
                $objectManager->flush();
            }
        }
    }

    public function supprimerUtilisateur(Participant $participant, $request, $validator, $listeChangementEtat, $objectManager)
    {
        if ($request->getContent() != null) {
            $listeChangementEtat = $request->getContent();
            $listeChangementEtat = $this->get('jms_serializer')->deserialize($listeChangementEtat, 'App\Entity\Participant', 'json');
            $error = $validator->validate($listeChangementEtat);
        } else {
            throw new \ErrorException("Aucune valeur recue !");
        }

        if($participant->isAdministrateur()=== true && $listeChangementEtat === null){
            foreach ($listeChangementEtat as $participant){
                $objectManager->remove($participant);
                $objectManager->flush();
            }
        }
    }
}
