<?php

namespace App\Repository;

use App\Entity\FlaggedBy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method FlaggedBy|null find($id, $lockMode = null, $lockVersion = null)
 * @method FlaggedBy|null findOneBy(array $criteria, array $orderBy = null)
 * @method FlaggedBy[]    findAll()
 * @method FlaggedBy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlaggedByRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FlaggedBy::class);
    }

    // /**
    //  * @return FlaggedBy[] Returns an array of FlaggedBy objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FlaggedBy
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
