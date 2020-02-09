<?php

namespace App\Repository;

use App\Entity\Blocks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Blocks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blocks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blocks[]    findAll()
 * @method Blocks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlocksRepository extends ServiceEntityRepository
{
    /**
     * BlocksRepository constructor.
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Blocks::class);
        $this->paginator = $paginator;
    }

    /**
     * @param int $page
     * @return Blocks
     */
    public function findAllBlocksPaginate($page = 1) : PaginationInterface {
         $query = $this->createQueryBuilder('b')
                ->getQuery();

        return $this->paginator->paginate($query, $page, 6);
    }

    // /**
    //  * @return Blocks[] Returns an array of Blocks objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Blocks
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
