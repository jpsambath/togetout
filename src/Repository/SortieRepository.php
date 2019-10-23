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
     * @param $organisateur
     * @param $inscrit
     * @param $pasInscrit
     * @param $etat
     * @param $texteRecherche
     * @param $idParticipant
     * @param $dateDebut
     * @param $dateFin
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findSortie($site,$organisateur,$inscrit,$pasInscrit,$sortiePassee,$texteRecherche,$idParticipant,$dateDebut,$dateFin)
    {
        //Les variables $inscrit, $organisateur, $pasInscrit et $sortiePassee sont des boolean.

        //Vérifie si le checkOrganisateur est cochée
        if ($organisateur){
            $organisteur = $idParticipant;
        }
        //Vérifie si le checkFiltreInscrit est cochée
        if ($inscrit){

        }

        //Vérifie si le checkFiltrePasInscrit est cochée
        if ($pasInscrit){

        }

        //Vérifie si le checkFiltreSortiePasse est cochée
        if ($sortiePassee){
            $etat="Passée";
        }else{
            $etat="";
        }

        $query = $this->createQueryBuilder('s')
            ->select('*')
            ->from('sortie')
            ->orWhere('s.site = :site')
            ->setParameter('site', $site);


            $query->orWhere('s.organisateur_id = :organisateur')
            ->setParameter('organisateur', $organisateur);


            $query->orWhere('s.inscrit = :inscrit')
            ->setParameter('inscrit', $inscrit);

            if ($i==$i){
                $query->orWhere('s.nom LIKE :texte')
                ->setParameter('texte','%'.$texteRecherche.'%');
            }


            $query->orWhere('s.etat = :etat')
            ->setParameter('etat', $etat);

            $query->orWhere('s.dateHeureDebut BETWEEN :debut AND :fin')
            ->setParameter('debut', $dateDebut)
            ->setParameter('fin', $dateFin);


            $query->getQuery()
            ->getResult();

            return $query;
    }



    /**
     * @param $organisateur
     * @return Sortie[] Returns an array of Sortie objects
     */
    /*public function findSortieByOrganisateur($organisateur)
{
    return $this->createQueryBuilder('s')
        ->andWhere('s.organisateur = :organisateur')
        ->setParameter('organisateur', $organisateur)
        ->getQuery()
        ->getResult()
        ;
}*/

    /**
     * @param $inscrit
     * @return Sortie[] Returns an array of Sortie objects
     */
    /*public function findSortieByInscrit($inscrit)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.inscrit = :inscrit')
            ->setParameter('inscrit', $inscrit)
            ->getQuery()
            ->getResult()
            ;
    }*/

    /**
     * @param $debut
     * @param $fin
     * @return Sortie[] Returns an array of Sortie objects
     */
    /*public function findSortieByIntervalleDate($debut,$fin)
    {
        return $this->createQueryBuilder('s')
            ->Where('s.dateHeureDebut BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getResult()
            ;
    }*/

    /**
     * @return Sortie[] Returns an array of Sortie objects
     * @throws Exception
     */
    /*public function findSortiePassees()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.etat = :etat')
            ->setParameter('etat', $etat)
            ->getQuery()
            ->getResult()
            ;
    }*/

    /**
     * @param $texteRecherche
     * @return Sortie[] Returns an array of Sortie objects
     */
    /*public function findSortieByNom($texteRecherche)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :texte')
            ->setParameter('texte','%'.$texteRecherche.'%')
            ->getQuery()
            ->getResult()
            ;
    }*/

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
