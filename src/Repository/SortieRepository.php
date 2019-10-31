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
     * @param string $ville
     * @param bool $cbxOrganisateur
     * @param bool $cbxInscrit
     * @param bool $cbxNonInscrit
     * @param bool $cbxPassees
     * @param string $recherche
     * @param $dateDebut
     * @param $dateFin
     * @param $heureDebut
     * @param $heureFin
     * @param $user
     * @param VilleRepository $villeRepository
     * @param LieuRepository $lieuRepository
     * @param EtatRepository $etatRepository
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findSortie(string $ville, bool $cbxOrganisateur, bool $cbxInscrit, bool $cbxNonInscrit, bool $cbxPassees,
                               string $recherche, $dateDebut, $dateFin, $heureDebut, $heureFin,
                               $user, VilleRepository $villeRepository, LieuRepository $lieuRepository , EtatRepository $etatRepository)
    {
        $dateTimeDebut = $dateDebut . " " . $heureDebut;
        $dateTimeFin = $dateFin . " " . $heureFin;

        //Filtre du nom de la ville
        $query = $this->createQueryBuilder('s')
            ->Where('s.lieu MEMBER OF :lieu')
            ->setParameter('lieu', $lieuRepository->findBy(["ville" => $villeRepository->findBy(["nom" => $ville])]));

        //Vérifie si le checkOrganisateur est cochée
        if ($cbxOrganisateur){
            $query->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $user);
        }

        //Filtre du nom de la sortie
        if (!empty($recherche)){
            $query->andWhere('s.nom LIKE :recherche')
                  ->setParameter('recherche', '%'.$recherche.'%');
        }

        //Vérifie si le checkFiltreSortiePasse est cochée
        if ($cbxPassees) {
            //Sélectionne les sortie passées
            $query->andWhere('s.etat = :etat')
                ->setParameter('etat', $etatRepository->findBy(["libelle" => EtatEnumType::PASSEE]));
        }else{
            //Sélectionne toutes les sorties sauf les sorties passées
            $query->andWhere('s.etat <> :etat')
                ->setParameter('etat', $etatRepository->findBy(["libelle" => EtatEnumType::PASSEE]));
        }

        //Vérifie si il y a un intervalle de date de saisie
        if((!empty(trim($dateTimeDebut))) && (!empty(trim($dateTimeFin)))){
            $query->andWhere('s.dateHeureDebut BETWEEN :debut AND :fin')
                ->setParameter('debut', $dateTimeDebut)
                ->setParameter('fin', $dateTimeFin);
        }

        //Vérifie si le checkFiltreInscrit est cochée
        if ($cbxInscrit){
            $query->andWhere(':user MEMBER OF s.participants')
                ->setParameter('user', $user);
        }

        //Vérifie si le checkFiltreNonInscrit est cochée
        if ($cbxInscrit){
            $query->andWhere(':user NOT MEMBER OF s.participants')
                ->setParameter('user', $user);
        }

        return $query->getQuery()->getResult();
    }


    public function loadSixProchaineSortiesInscritUtilisateur(Participant $participant, EtatRepository $etatRepository)
    {
        $qb = $this->createQueryBuilder('s');

        $condition1 = $qb->expr()->andX(
            $qb->expr()->isMemberOf(':val', 's.participants')
        );
        $qb->setParameter('val', $participant);

        $condition2 = $qb->expr()->orX(
            $qb->expr()->eq('s.etat', ':etat'),
            $qb->expr()->eq('s.etat', ':etat2')
        );
        $qb->setParameter('etat', $etatRepository->findBy(["libelle" => EtatEnumType::OUVERTE]))
            ->setParameter('etat2', $etatRepository->findBy(["libelle" => EtatEnumType::CLOTUREE]));

        $qb->where($qb->expr()->andX($condition1 , $condition2))
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->setMaxResults(6);

        return $qb->getQuery()->getResult();
    }


    public function loadSixProchaineSortiesProposeUtilisateur(Participant $participant,  EtatRepository $etatRepository)
    {
        $qb = $this->createQueryBuilder('s');

        $condition1 = $qb->expr()->andX(
            $qb->expr()->eq('s.organisateur', ':val')
        );
        $qb->setParameter('val', $participant);

        $condition2 = $qb->expr()->orX(
            $qb->expr()->eq('s.etat', ':etat'),
            $qb->expr()->eq('s.etat', ':etat2'),
            $qb->expr()->eq('s.etat', ':etat3')
        );
        $qb->setParameter('etat', $etatRepository->findBy(["libelle" => EtatEnumType::CREE]))
            ->setParameter('etat2', $etatRepository->findBy(["libelle" => EtatEnumType::OUVERTE]))
            ->setParameter('etat3', $etatRepository->findBy(["libelle" => EtatEnumType::CLOTUREE]));

        $qb->where($qb->expr()->andX($condition1 , $condition2))
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->setMaxResults(6);

        return $qb->getQuery()->getResult();
    }


    public function sixProchaineSortie(EtatRepository $etatRepository)
    {
        $now = Carbon::now();
        $endOfWeek = Carbon::now()->endOfWeek();

        $qb = $this->createQueryBuilder('s');

        $condition1 = $qb->expr()->andX(
            $qb->expr()->between('s.dateHeureDebut', ':from', ':to')
        );
        $qb->setParameter('from', $now)
            ->setParameter('to', $endOfWeek);

        $condition2 = $qb->expr()->orX(
            $qb->expr()->eq('s.etat', ':etat'),
            $qb->expr()->eq('s.etat', ':etat2')
        );
        $qb->setParameter('etat', $etatRepository->findBy(["libelle" => EtatEnumType::OUVERTE]))
            ->setParameter('etat2', $etatRepository->findBy(["libelle" => EtatEnumType::CLOTUREE]));

        $qb->where($qb->expr()->andX($condition1 , $condition2))
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->setMaxResults(6);

        return $qb->getQuery()->getResult();
    }


    public function sixProchainesSortiesSemaineSuivante(EtatRepository $etatRepository)
    {
        $nextWeekBegin = Carbon::now()->addWeek()->startOfWeek();
        $nextWeekEnd = Carbon::now()->addWeek()->endOfWeek();

        $qb = $this->createQueryBuilder('s');

        $condition1 = $qb->expr()->andX(
            $qb->expr()->between('s.dateHeureDebut', ':from', ':to')
        );
        $qb->setParameter('from', $nextWeekBegin)
            ->setParameter('to', $nextWeekEnd);

        $condition2 = $qb->expr()->orX(
            $qb->expr()->eq('s.etat', ':etat'),
            $qb->expr()->eq('s.etat', ':etat2')
        );
        $qb->setParameter('etat', $etatRepository->findBy(["libelle" => EtatEnumType::OUVERTE]))
            ->setParameter('etat2', $etatRepository->findBy(["libelle" => EtatEnumType::CLOTUREE]));

        $qb->where($qb->expr()->andX($condition1 , $condition2))
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->setMaxResults(6);

        return $qb->getQuery()->getResult();
    }


    public function getSortieWhereUserIs(Participant $participant)
    {
        return $this->createQueryBuilder('s')
            ->Where(':val MEMBER OF s.participants')
            ->setParameter('val', $participant)
            ->getQuery()
            ->getResult();
    }

    public function findAllSortie(Participant $participant, EtatRepository $etatRepository)
    {
        {
            $qb = $this->createQueryBuilder('s');

            $condition1 = $qb->expr()->andX(
                $qb->expr()->neq('s.etat', ':etat'),
                $qb->expr()->neq('s.organisateur', ':val')
            );
            $qb->setParameter('val', $participant)
                ->setParameter('etat', $etatRepository->findBy(["libelle" => EtatEnumType::CREE]));

            $condition2 = $qb->expr()->andX(
                $qb->expr()->neq('s.etat', ':etat'),
                $qb->expr()->eq('s.organisateur', ':val')
            );
            $qb->setParameter('val', $participant)
                ->setParameter('etat', $etatRepository->findBy(["libelle" => EtatEnumType::CREE]));

            $qb->where($qb->expr()->orX($condition1, $condition2))
                ->orderBy('s.dateHeureDebut', 'DESC');

            return $qb->getQuery()->getResult();
        }
    }

}
