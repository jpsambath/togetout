<?php

namespace App\Controller;

use App\Entity\ManagerJSON;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SortieController
 * @package App\Controller
 * @Route("/api", name="")
 */
class SortieController extends Controller
{

    /*-------------------------------------DAVID----------------------------------------------*/

    /**
     * @Route("/inscriptionSortie/{id}", name="inscriptionSortie")
     * @param Sortie $sortie
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return Response
     */
    public function inscriptionSortie(Sortie $sortie, SerializerInterface $serializer, Request $request)
    {
        try{
            ManagerJSON::testRecupJSON($request);

            $sortie->addParticipant($this->getUser());

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Update successfull";

        } catch (Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "inscriptionSortie";
        }
        return ManagerJSON::renvoiJSON($tab, $serializer);
    }

    /**
     * @Route("/desistementSortie/{id}", name="desistementSortie")
     * @param Sortie $sortie
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return Response
     */
    public function desistementSortie(Sortie $sortie, SerializerInterface $serializer, Request $request)
    {
        try{
            ManagerJSON::testRecupJSON($request);

            $sortie->removeParticipant($this->getUser());

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Update successfull";

        } catch (Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "desistementSortie";
        }
        return ManagerJSON::renvoiJSON($tab, $serializer);
    }

    /**
     * @Route("/clotureInscription", name="clotureInscription")
     * @param Sortie $sortie
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return Response
     */
    public function clotureInscrition(Sortie $sortie, SerializerInterface $serializer, Request $request)
    {
        try{
            ManagerJSON::testRecupJSON($request);

            if ($sortie->getDateLimiteInscription() <= (new \DateTime())) {
                $sortie->inscriptionCloturee($sortie);
            }

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Update successfull";

        } catch (Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "clotureInscrition";
        }
        return ManagerJSON::renvoiJSON($tab, $serializer);
    }

    /**
     * @Route("/annulerSorite/{id}", name="annulertSortie")
     * @param Sortie $sortieAnnule
     * @param ObjectManager $objectManager
     * @param Participant $participant
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return Response
     */
    public function annulationSortie(Sortie $sortieAnnule, ObjectManager $objectManager, Participant $participant, SerializerInterface $serializer, Request $request)
    {
        try{
            ManagerJSON::testRecupJSON($request);

            if (!($participant->getId() === $sortieAnnule->getOrganisateur()->getId() || $participant->isAdministrateur())) {
                throw new ErrorException('Vous n\'avez pas les droits!');
            }

            $objectManager->remove($sortieAnnule);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab["messageOk"] = "La sortie a ete annulee avec succes !";

        } catch (\Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "annulationSortie";
        }

        return ManagerJSON::renvoiJSON($tab, $serializer);
    }

    /**
     * @Route("/creerSortie")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param ObjectManager $objectManager
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function creerSortie(Request $request, ValidatorInterface $validator, ObjectManager $objectManager,  SerializerInterface $serializer)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            $sortieRecu = $request->getContent();
            $sortieRecu =$serializer->deserialize($sortieRecu, Sortie::class, 'json');
            $error = $validator->validate($sortieRecu);

            if (count($error) > 0) {
                throw new \ErrorException("Erreur lors de la validation !");
            }

            $objectManager->persist($sortieRecu);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Sortie creer avec success !";

        } catch (Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "creerSortie";
        }

        return ManagerJSON::renvoiJSON($tab, $serializer);
    }
    /*------------------------------------ANTOINE--------------------------------------------*/
    /**
     * @Route("/listeSorties", name="listeSorties")
     * @param Request $request
     * @param ObjectManager $objectManager
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function listeSorties(Request $request, ObjectManager $objectManager, SerializerInterface $serializer)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            $listeSortie = $objectManager
                ->getRepository(Sortie::class)
                ->findSortie(json_decode($request->get("selectSite")), //Id du site selectionné
                    json_decode($request->get("checkFiltreOrganisateur")), //Boolean
                    json_decode($request->get("checkFiltreInscrit")), //Boolean
                    json_decode($request->get("checkFiltrePasInscrit")), //Boolean
                    json_decode($request->get("checkFiltreSortiePasse")), //Boolean
                    json_decode($request->get("filtreSaisieNom")), //Valeur de l'input
                    json_decode($request->get("idParticipant")), //Id de l'utilisateur connecté
                    json_decode($request->get("filtreDateDebut")), //valeur de l'input de type date du premier intervalle
                    json_decode($request->get("filtreDateFin"))); //valeur de l'input de type date du deuxième intervalle

            $tab['statut'] = "ok";
            $tab['listeSortie'] = $listeSortie;

        } catch (Exception $e){
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "listeSorties";
        }

        return ManagerJSON::renvoiJSON($tab, $serializer);
    }


    /**
     * @Route("/getSortieInfo", name="getSortieInfo")
     * @param SortieRepository $repository
     * @param SerializerInterface $serializer
     * @param ObjectManager $objectManager
     * @return Response
     */
    public function getSortieInfo(SortieRepository $repository, SerializerInterface $serializer, ObjectManager $objectManager)
    {
        try{
            $participant = ManagerJSON::test($this->getUser(), $objectManager);

            $tab['sortiesInscrits'] = $repository->loadSixProchaineSortiesInscritUtilisateur($participant);
            $tab['sortiesOrganisateurs'] = $repository->loadSixProchaineSortiesProposeUtilisateur($participant);
            $tab['sortiesSemaineActuelle'] = $repository->sixProchaineSortie();
            $tab['sortiesSemaineProchaine'] = $repository->sixProchainesSortiesSemaineSuivante();

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Update successfull";

        } catch (Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "accueil";
        }
        return ManagerJSON::renvoiJSON($tab, $serializer);
    }
}
