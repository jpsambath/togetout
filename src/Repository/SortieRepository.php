<?php

namespace App\Repository;

use App\DBAL\Types\EtatEnumType;
use App\Entity\Participant;
use App\Entity\Sortie;
use Carbon\Carbon;
use DateInterval as DateIntervalAlias;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @param $idSite
     * @param $organisateur
     * @param $inscrit
     * @param $pasInscrit
     * @param $sortiePassee
     * @param $texteRecherche
     * @param $idParticipant
     * @param $dateDebut
     * @param $dateFin
     * @return Sortie[] Returns an array of Sortie objects
     * @throws Exception
     */
    public function findSortie($idSite,$organisateur,$inscrit,$pasInscrit,$sortiePassee,$texteRecherche,$idParticipant,$dateDebut,$dateFin)
    {
        //Les variables $inscrit, $organisateur, $pasInscrit et $sortiePassee sont des boolean.

        $query = $this->createQueryBuilder('s')
            //->select('*')
            //->from('sortie','s')
            ->Where('s.site_id = :idSite')
            ->setParameter('idSite', $idSite);

            //Vérifie si le checkOrganisateur est cochée
            if ($organisateur){
                $query->orWhere('s.organisateur_id = :organisateur')
                    ->setParameter('organisateur', $idParticipant);
            }

            //Vérifie si il y a une saisie de nom
            if (!empty($texteRecherche)){
                $query->orWhere('s.nom LIKE :texte')
                      ->setParameter('texte','%'.$texteRecherche.'%');
            }

            //Vérifie si le checkFiltreSortiePasse est cochée
            if ($sortiePassee) {
                //Sélectionne les sortie passées
                $date= New \DateTime();
                $query->orWhere('s.etat = :etat')
                      ->setParameter('etat', 'Passée')
                      ->andWhere('s.dateHeureDebut > :dateDuJourOneMonth')
                      ->setParameter('dateDuJourOneMonth', $date->sub(new DateIntervalAlias('P1M')));
            }else{
                //Sélectionne toutes les sorties sauf les sorties passées
                $query->orWhere('s.etat <> :etat')
                    ->setParameter('etat', 'Passée');
            }

            //Vérifie si il y a un intervalle de date de saisie
            if(empty($dateDebut)==false and empty($dateFin)==false){
                $query->orWhere('s.dateHeureDebut BETWEEN :debut AND :fin')
                    ->setParameter('debut', $dateDebut)
                    ->setParameter('fin', $dateFin);
            }

            $liste = $query->getQuery()
            ->getResult();

        $listeSortie[]="";
        if ($inscrit ==false and $pasInscrit ==false){
            foreach ($liste as $sortie){
                    $listeSortie[]=$sortie;
                }
        }else{
            //Vérifie si le checkFiltreInscrit est cochée
            if ($inscrit){
                foreach ($liste as $sortie){
                    if($sortie->getInscrit == $idParticipant){
                        $listeSortie[]=$sortie;
                    }
                }
            }

            //Vérifie si le checkFiltrePasInscrit est cochée
            if ($pasInscrit){
                foreach ($liste as $sortie){
                    if($sortie->getInscrit == $idParticipant){
                        $listeSortie[]=$sortie;
                    }
                }
            }
        }
            return $listeSortie;
    }


    public function loadSixProchaineSortiesInscritUtilisateur(Participant $participant, EtatRepository $etatRepository)
    {

        return $this->createQueryBuilder('s')
            ->Where(':val MEMBER OF s.participants')
            ->setParameter('val', $participant)
            ->andWhere('s.etat = :etat')
            ->setParameter('etat', $etatRepository->findBy(["libelle" => EtatEnumType::OUVERTE]))
            ->orWhere('s.etat = :etat2')
            ->setParameter('etat2', $etatRepository->findBy(["libelle" => EtatEnumType::CLOTUREE]))
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->setMaxResults(6)->getQuery()->getResult();

    }

    public function loadSixProchaineSortiesProposeUtilisateur(Participant $participant,  EtatRepository $etatRepository)
    {
        return $this->createQueryBuilder('s')
            ->Where('s.etat = :etat')
            ->setParameter('etat', $etatRepository->findBy(["libelle" => EtatEnumType::CREE]))
            ->orWhere('s.etat = :etat2')
            ->setParameter('etat2', $etatRepository->findBy(["libelle" => EtatEnumType::OUVERTE]))
            ->orWhere('s.etat = :etat3')
            ->setParameter('etat3', $etatRepository->findBy(["libelle" => EtatEnumType::CLOTUREE]))
            ->andWhere('s.organisateur = :val')
            ->setParameter('val', $participant)
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->setMaxResults(6)->getQuery()->getResult();
    }

    public function sixProchaineSortie(EtatRepository $etatRepository)
    {
        $now = Carbon::now();
        $endOfWeek = Carbon::now()->endOfWeek();

        return $this->createQueryBuilder('s')
            ->Where('s.dateHeureDebut BETWEEN :from AND :to')
            ->setParameter('from', $now)
            ->setParameter('to', $endOfWeek)
            ->andWhere('s.etat = :etat')
            ->setParameter('etat', $etatRepository->findBy(["libelle" => EtatEnumType::OUVERTE]))
            ->orWhere('s.etat = :etat2')
            ->setParameter('etat2', $etatRepository->findBy(["libelle" => EtatEnumType::CLOTUREE]))
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->setMaxResults(6)->getQuery()->getResult();
    }

    public function sixProchainesSortiesSemaineSuivante(EtatRepository $etatRepository)
    {
        $nextWeekBegin = Carbon::now()->addWeek()->startOfWeek();
        $nextWeekEnd = Carbon::now()->addWeek()->endOfWeek();

        return $this->createQueryBuilder('s')
            ->Where('s.dateHeureDebut BETWEEN :from AND :to')
            ->setParameter('from', $nextWeekBegin)
            ->setParameter('to', $nextWeekEnd)
            ->andWhere('s.etat = :etat')
            ->setParameter('etat', $etatRepository->findBy(["libelle" => EtatEnumType::OUVERTE]))
            ->orWhere('s.etat = :etat2')
            ->setParameter('etat2', $etatRepository->findBy(["libelle" => EtatEnumType::CLOTUREE]))
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->setMaxResults(6)->getQuery()->getResult();
    }

}
