<?php

namespace App\Repository;

use App\Entity\Cards;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Cards|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cards|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cards[]    findAll()
 * @method Cards[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardsRepository extends ServiceEntityRepository
{

    /**
     * CardsRepository constructor.
     * @var PaginatorInterface
     */
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Cards::class);
        $this->paginator = $paginator;
    }

    /**
     * @param Int $id
     * @return Cards
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findOneByIdWithData (Int $id)
    {
        return $this->createQueryBuilder('c')
            ->addSelect('color', 'type', 'rarity', 'sets')
            ->join('c.cardColor', 'color')
            ->join('c.cardType', 'type')
            ->join('c.cardRarity', 'rarity')
            ->join('c.cardSet', 'sets')

            ->andWhere('c.cardId = :cardId')
            ->setParameter('cardId', $id)

            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param String $set_name
     * @param $order
     * @return Cards[]
     */
    public function findBySetName(String $set_name, $order)
    {
        if ($order != NULL) {
            $order_by = "c.".$order;
        }
        else {
            $order_by = "c.cardSetNum";
        }
        return $this->createQueryBuilder('c')
            ->addSelect('color', 'type', 'rarity', 'sets')
            ->leftJoin('c.cardColor', 'color')
            ->leftjoin('c.cardType', 'type')
            ->leftjoin('c.cardRarity', 'rarity')
            ->leftjoin('c.cardSet', 'sets')

            ->andWhere('sets.name = :set_name')
            ->setParameter('set_name', $set_name)

            ->orderBy($order_by, 'ASC')

            ->getQuery()
            ->getArrayResult();
    }

    public function SearchCardsLike($title, $page = 1) : PaginationInterface
    {
        $query = $this->createQueryBuilder('c')
            ->addSelect('color', 'type', 'rarity', 'sets', 'block')

            ->leftJoin('c.cardColor', 'color')
            ->leftjoin('c.cardType', 'type')
            ->leftjoin('c.cardRarity', 'rarity')
            ->leftjoin('c.cardSet', 'sets')
            ->leftJoin('sets.block', 'block')

            ->where('c.cardName LIKE :title')
            ->setParameter('title', '%'.$title.'%')

            ->getQuery();

        return $this->paginator->paginate($query, $page, 5);
    }

    public function CountSearchResult($title)
    {
        try {
            return $this->createQueryBuilder('c')
                ->select('count(c) as total')
                ->where('c.cardName LIKE :title')
                ->setParameter('title', '%' . $title . '%')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
        }
    }

    /* utilisé pour le crud */
    public function FindAllWithPagination($page = 1) : PaginationInterface
    {
        $query = $this->createQueryBuilder('c')
            ->addSelect('color', 'type', 'rarity', 'sets', 'block')

            ->leftJoin('c.cardColor', 'color')
            ->leftjoin('c.cardType', 'type')
            ->leftjoin('c.cardRarity', 'rarity')
            ->leftjoin('c.cardSet', 'sets')
            ->leftJoin('sets.block', 'block')

            ->getQuery();

        return $this->paginator->paginate($query, $page, 50);
    }

    // /**
    //  * @return Cards[] Returns an array of Cards objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cards
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


}
