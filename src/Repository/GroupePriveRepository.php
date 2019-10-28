<?php

namespace App\Repository;

use App\Entity\GroupePrive;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method GroupePrive|null find($id, $lockMode = null, $lockVersion = null)
 * @method GroupePrive|null findOneBy(array $criteria, array $orderBy = null)
 * @method GroupePrive[]    findAll()
 * @method GroupePrive[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupePriveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupePrive::class);
    }

    // /**
    //  * @return GroupePrive[] Returns an array of GroupePrive objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GroupePrive
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
