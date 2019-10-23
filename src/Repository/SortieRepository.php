<?php

namespace App\Repository;

use App\Entity\Sortie;
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


    /**
     * @param $site
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findSortieBySite($site)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.site = :site')
            ->setParameter('site', $site)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $organisateur
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findSortieByOrganisateur($organisateur)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.organisateur = :organisateur')
            ->setParameter('organisateur', $organisateur)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $inscrit
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findSortieByInscrit($inscrit)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.inscrit = :inscrit')
            ->setParameter('inscrit', $inscrit)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $debut
     * @param $fin
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findSortieByIntervalleDate($debut,$fin)
    {
        return $this->createQueryBuilder('s')
            ->Where('s.dateHeureDebut BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     * @throws Exception
     */
    public function findSortiePassees()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut < :dateDuJour')
            ->setParameter('dateDuJour', new \DateTime("now"))
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $texteRecherche
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findSortieByNom($texteRecherche)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :texte')
            ->setParameter('texte','%'.$texteRecherche.'%')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $sortie
     * @throws Exception
     */


    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


}
