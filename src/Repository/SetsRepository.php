<?php

namespace App\Repository;

use App\Entity\Sets;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sets|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sets|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sets[]    findAll()
 * @method Sets[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SetsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sets::class);
    }

    /**
     * @return Sets[]
     */
    public function findAllWithBlocks()
    {
        return  $this->createQueryBuilder('s')// Alias (b) va représenter l'entité
            ->leftJoin('s.block', 'block')
            ->addSelect('block')
            ->getQuery()
            ->getArrayResult();
    }

    // /**
    //  * @return Sets[] Returns an array of Sets objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sets
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
