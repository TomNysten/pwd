<?php

namespace App\Repository;

use App\Entity\Comments;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Comments|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comments|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comments[]    findAll()
 * @method Comments[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentsRepository extends ServiceEntityRepository
{

    private $paginator;

    /**
     * CommentsRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param PaginatorInterface $paginator
     */

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Comments::class);
        $this->paginator = $paginator;
    }

    /**
     * @return Comments[]
     * @param Int $id
     */
    public function findByPostedOnCardWithAuthor (Int $id) {
        return $this->createQueryBuilder('com')
            ->addSelect('auteur')
            ->join('com.auteur', 'auteur')
            ->andWhere('com.postedOnCard = :cardId')
            ->setParameter('cardId', $id)

            ->setMaxResults(5)
            ->orderBy('com.postedAt', 'DESC')

            ->getQuery()
            ->getArrayResult();
    }
    /**
     * @return Comments[]
     * @param string $author
     */
    public function findAllWithLocation (string $author) {
        return $this->createQueryBuilder('com')
            ->addSelect('auteur', 'onCard', 'onUser', 'cardSet')
            ->leftJoin('com.auteur'      , 'auteur')
            ->leftJoin('com.postedOnCard', 'onCard')
            ->leftJoin('onCard.cardSet', 'cardSet')
            ->leftJoin('com.postedOnUser', 'onUser')

            ->andWhere('auteur.username = :author')
            ->setParameter('author', $author)

            ->orderBy('com.postedAt', 'DESC')
            ->setMaxResults(10)

            ->getQuery()
            ->getResult();
    }

    public function findAllFromCardWithPagination ($cardId, $page = 1) : PaginationInterface {
        $query = $this->createQueryBuilder('com')
            ->addSelect('auteur')
            ->join('com.auteur', 'auteur')
            ->andWhere('com.postedOnCard = :cardId')
            ->setParameter('cardId', $cardId)

            ->orderBy('com.postedAt', 'DESC')
            ->getQuery();

        return $this->paginator->paginate($query, $page, 10);
    }

    public function findAllFromAuthorWithPagination ($auteur, $page = 1) : PaginationInterface {
        $query = $this->createQueryBuilder('com')
            ->addSelect('auteur', 'OnUser', 'OnCard')

            ->leftJoin('com.auteur', 'auteur')
            ->leftJoin('com.postedOnUser', 'OnUser')
            ->leftJoin('com.postedOnCard', 'OnCard')

            ->andWhere('auteur.username = :auteur')
            ->setParameter('auteur', $auteur)

            ->orderBy('com.postedAt', 'DESC')
            ->getQuery();

        return $this->paginator->paginate($query, $page, 10);
    }

    public function findAllOnProfileWithPagination ($username, $page = 1) : PaginationInterface {
        $query = $this->createQueryBuilder('com')
            ->addSelect('auteur')
            ->leftJoin('com.auteur', 'auteur')
            ->leftJoin('com.postedOnUser', 'posted_on_user')
            ->andWhere('posted_on_user.username = :user')
            ->setParameter('user', $username)

            ->orderBy('com.postedAt', 'DESC')
            ->getQuery();

        return $this->paginator->paginate($query, $page, 10);
    }

    public function findAllWithPaginationAndFlags($page = 1) : PaginationInterface
    {
        $query = $this->createQueryBuilder('com')
            ->addSelect('user')
            ->leftJoin('com.FlaggedByUsers', 'user')
            //->leftJoin('com.postedOnUser', 'posted_on_user')


            //->addOrderBy('com.FlaggedByUsers', 'DESC')
            ->addOrderBy('com.postedAt', 'DESC')

            ->getQuery();

        return $this->paginator->paginate($query, $page, 10);
    }
    // /**
    //  * @return Comments[] Returns an array of Comments objects
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
    public function findOneBySomeField($value): ?Comments
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
