<?php

namespace App\Repository;

use App\Entity\Participant;
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
     * @param $site
     * @param $organisateur
     * @param $inscrit
     * @param $pasInscrit
     * @param $sortiePassee
     * @param $texteRecherche
     * @param $idParticipant
     * @param $dateDebut
     * @param $dateFin
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findSortie($idSite,$organisateur,$inscrit,$pasInscrit,$sortiePassee,$texteRecherche,$idParticipant,$dateDebut,$dateFin)
    {
        //Les variables $inscrit, $organisateur, $pasInscrit et $sortiePassee sont des boolean.

        $query = $this->createQueryBuilder('s')
            ->select('*')
            ->from('sortie','s')
            ->orWhere('s.site_id = :idSite')
            ->setParameter('idSite', $idSite);

            //Vérifie si le checkOrganisateur est cochée
            if ($organisateur){
                $query->orWhere('s.organisateur_id = :organisateur')
                    ->setParameter('organisateur', $idParticipant);
            }

            //Vérifie si il y a une saisie de nom
            if (empty($texteRecherche)==false){
                $query->orWhere('s.nom LIKE :texte')
                      ->setParameter('texte','%'.$texteRecherche.'%');
            }

            //Vérifie si le checkFiltreSortiePasse est cochée
            if ($sortiePassee) {
                //Sélectionne les sortie passées
                $query->orWhere('s.etat = :etat')
                      ->setParameter('etat', 'Passée');
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

}
