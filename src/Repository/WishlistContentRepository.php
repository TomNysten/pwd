<?php

namespace App\Repository;

use App\Entity\WishlistContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method WishlistContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method WishlistContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method WishlistContent[]    findAll()
 * @method WishlistContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WishlistContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WishlistContent::class);
    }

    /*public function FindWishlistContentFromWishlist($user_id, $setname) {
        return $this->createQueryBuilder('w')
            ->addSelect('cards', 'card_set', 'card_color', 'card_type', 'card_rarity')

            ->leftJoin('cc.cards', 'cards')
            ->leftJoin('cards.cardSet', 'card_set')
            ->leftJoin('cards.cardColor', 'card_color')
            ->leftJoin('cards.cardType', 'card_type')
            ->leftJoin('cards.cardRarity', 'card_rarity')

            ->andWhere('cc.fromCollection = :user_id')
            ->andWhere('card_set.name = :set')

            ->setParameter('user_id', $user_id)
            ->setParameter('set', $setname)

            ->getQuery()
            ->getArrayResult();
    }*/

    // /**
    //  * @return WishlistContent[] Returns an array of WishlistContent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WishlistContent
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
