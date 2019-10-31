<?php

namespace App\Controller;

use App\DBAL\Types\EtatEnumType;
use App\Entity\ManagerJSON;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use Exception;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SortieController
 * @package App\Controller
 * @Route("/api", name="")
 */
class SortieController extends Controller
{

    /**
     * @Route("/inscriptionSortie/{id}", name="inscriptionSortie", requirements={"id": "\d+"})
     * @param Sortie $sortie
     * @param ObjectManager $objectManager
     * @return Response
     */
    public function inscriptionSortie(Sortie $sortie, ObjectManager $objectManager)
    {
        try{
            $participant = ManagerJSON::getUser($this->getUser(), $objectManager);
            $sortie->addParticipant($participant[0]);

            $objectManager->persist($sortie);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Update successfull";

        } catch (Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "inscriptionSortie";
        }
        return ManagerJSON::renvoiJSON($tab);
    }

    /**
     * @Route("/desistementSortie/{id}", name="desistementSortie", requirements={"id": "\d+"})
     * @param Sortie $sortie
     * @param Request $request
     * @param ObjectManager $objectManager
     * @return Response
     */
    public function desistementSortie(Sortie $sortie, Request $request, ObjectManager $objectManager)
    {
        try{
            ManagerJSON::testRecupJSON($request);

            $participant = ManagerJSON::getUser($this->getUser(), $objectManager);
            $sortie->removeParticipant($participant[0]);

            $objectManager->persist($sortie);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Update successfull";

        } catch (Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "desistementSortie";
        }
        return ManagerJSON::renvoiJSON($tab);
    }

    /**
     * @Route("/clotureInscription", name="clotureInscription")
     * @param Sortie $sortie
     * @param Request $request
     * @return Response
     */
    public function clotureInscrition(Sortie $sortie, Request $request)
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
        return ManagerJSON::renvoiJSON($tab);
    }

    /**
     * @Route("/annulerSorite/{id}", name="annulertSortie", requirements={"id": "\d+"})
     * @param Sortie $sortieAnnule
     * @param ObjectManager $objectManager
     * @param Participant $participant
     * @param Request $request
     * @return Response
     */
    public function annulationSortie(Sortie $sortieAnnule, ObjectManager $objectManager, Participant $participant, Request $request)
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

        return ManagerJSON::renvoiJSON($tab);
    }

    /**
     * @Route("/creerSortie")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param ObjectManager $objectManager
     * @return Response
     */
    public function creerSortie(Request $request, ValidatorInterface $validator, ObjectManager $objectManager)
    {
        try {
            ManagerJSON::testRecupJSON($request);
            $organisateur = ManagerJSON::getUser($this->getUser(), $objectManager);

            $sortieRecu = $request->getContent();

            $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
            $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [new JsonEncoder()]);

            $sortieDeserialise =$serializer->deserialize($sortieRecu, Sortie::class, 'json');

            $errors = $validator->validate($sortieDeserialise);

            if (count($errors) > 0) {
                $messageErreur = '';
                foreach ($errors as $error){
                    $messageErreur = $messageErreur . "\n" . $error;
                }
                throw new ErrorException($messageErreur);
            }

            $sortieDeserialise->setOrganisateur($organisateur[0]);

            $etat = $objectManager->merge($sortieDeserialise->getEtat());
            $sortieDeserialise->setEtat($etat);

            if ($sortieDeserialise->getEtat()->getLibelle() == EtatEnumType::OUVERTE){
                $sortieDeserialise->addParticipant($organisateur[0]);
            }

            $lieu = $objectManager->merge($sortieDeserialise->getLieu());
            $sortieDeserialise->setLieu( $lieu);

            $sortieDeserialise->setSite($organisateur[0]->getSite());

            $objectManager->persist($sortieDeserialise);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Sortie creer avec success !";

        } catch (Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "creerSortie";
        }

        return ManagerJSON::renvoiJSON($tab);
    }


    /**
     * @Route("/getSearchSorties", name="getSearchSorties")
     * @param Request $request
     * @param ObjectManager $objectManager
     * @param VilleRepository $villeRepository
     * @param LieuRepository $lieuRepository
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function getSearchSorties(Request $request, ObjectManager $objectManager, VilleRepository $villeRepository, LieuRepository $lieuRepository, EtatRepository $etatRepository)
    {
        try {
            ManagerJSON::testRecupJSON($request);
            $data = json_decode($request->getContent(), true);
            $user = ManagerJSON::getUser($this->getUser(), $objectManager);

            $searchSorties = $objectManager
                ->getRepository(Sortie::class)
                ->findSortie($data["ville"], //Ville selectionné
                    $data["cbxOrganisateur"], //Boolean
                    $data["cbxInscrit"], //Boolean
                    $data["cbxNonInscrit"], //Boolean
                    $data["cbxPassees"], //Boolean
                    $data["recherche"], //Valeur de l'input (nom de la sortie)
                    $data["dateDebut"], //valeur de l'input de type date du premier intervalle
                    $data["dateFin"], //valeur de l'input de type date du deuxième intervalle
                    $data["heureDebut"],
                    $data["heureFin"],
                    $user,
                    $villeRepository,
                    $lieuRepository,
                    $etatRepository);

            $tab['statut'] = "ok";
            $tab['searchSorties'] = $searchSorties;

        } catch (Exception $e){
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "getSearchSorties";
        }

        return ManagerJSON::renvoiJSON($tab);
    }


    /**
     * @Route("/getSortieInfo", name="getSortieInfo")
     * @param SortieRepository $repository
     * @param ObjectManager $objectManager
     * @param EtatRepository $etatRepository
     * @return Response
     */
    public function getSortieInfo(SortieRepository $repository, ObjectManager $objectManager, EtatRepository $etatRepository)
    {
        try{
            $participant = ManagerJSON::getUser($this->getUser(), $objectManager);

            $tab['sortiesInscrits'] = $repository->loadSixProchaineSortiesInscritUtilisateur($participant[0],$etatRepository);
            $tab['sortiesOrganisateurs'] = $repository->loadSixProchaineSortiesProposeUtilisateur($participant[0],$etatRepository);
            $tab['sortiesSemaineActuelle'] = $repository->sixProchaineSortie($etatRepository);
            $tab['sortiesSemaineProchaine'] = $repository->sixProchainesSortiesSemaineSuivante($etatRepository);

            $tab['statut'] = "ok";

        } catch (Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "getSortieInfo";
        }
        return ManagerJSON::renvoiJSON($tab);
    }

    /**
     * @Route("/getSortie/{id}", name="getSortie", requirements={"id": "\d+"})
     * @param Sortie $sortie
     * @return Response
     */
    public function getSortie(Sortie $sortie)
    {
        try{
            $tab['statut'] = "ok";
            $tab['sortie'] =  $sortie;

        } catch (Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "getSortie";
        }
        return ManagerJSON::renvoiJSON($tab);
    }

    /**
     * @Route("/getAllSortie", name="getAllSortie")
     * @param SortieRepository $sortieRepository
     * @param EtatRepository $etatRepository
     * @param ObjectManager $objectManager
     * @return Response
     */
    public function getAllSortie(SortieRepository $sortieRepository, EtatRepository $etatRepository, ObjectManager $objectManager)
    {
        try{
            $participant = ManagerJSON::getUser($this->getUser(), $objectManager);

            $allSortie = $sortieRepository->findAllSortie($participant[0], $etatRepository);

            $tab['statut'] = "ok";
            $tab['allSortie'] =  $allSortie;

        } catch (Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "getAllSortie";
        }
        return ManagerJSON::renvoiJSON($tab);
    }

    /**
     * @Route("/modifierSortie", name="modifierSortie")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param ObjectManager $objectManager
     * @return Response
     */
    public function modifierSortie(Request $request, ValidatorInterface $validator, ObjectManager $objectManager, SerializerInterface $serializer)
    {
        try{
            ManagerJSON::testRecupJSON($request);

            $sortieRecu = $request->getContent();

            //$normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
            //$serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [new JsonEncoder()]);

            $sortieDeserialise = $serializer->deserialize($sortieRecu, Sortie::class, 'json');

            $sortieDeserialise = $objectManager->merge($sortieDeserialise);

            $errors = $validator->validate($sortieDeserialise);

            if (count($errors) > 0) {
                $messageErreur = '';
                foreach ($errors as $error){
                    $messageErreur = $messageErreur . "\n" . $error;
                }
                throw new ErrorException($messageErreur);
            }

            $participant = $objectManager->merge($sortieDeserialise->getParticipants());
            $sortieDeserialise->setParticipants($participant);
            $organisateur = $objectManager->merge($sortieDeserialise->getOrganisateur());
            $sortieDeserialise->setOrganisateur($organisateur);
            $etat = $objectManager->merge($sortieDeserialise->getEtat());
            $sortieDeserialise->setEtat($etat);
            $site = $objectManager->merge($sortieDeserialise->getSite());
            $sortieDeserialise->setSite($site);
            $lieu = $objectManager->merge($sortieDeserialise->getLieu());
            $sortieDeserialise->setLieu($lieu);
            $groupesPrive = $objectManager->merge($sortieDeserialise->getGroupesPrive());
            $sortieDeserialise->setGroupesPrive($groupesPrive);

            $objectManager->persist($sortieDeserialise);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Update successfull";

        } catch (\Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "modifierSortie";
        }

        return ManagerJSON::renvoiJSON($tab);
    }
}
