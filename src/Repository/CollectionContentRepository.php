<?php

namespace App\Repository;

use App\Entity\CollectionContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CollectionContent|null find($id, $lockMode = null, $lockVersion = null)
 * @method CollectionContent|null findOneBy(array $criteria, array $orderBy = null)
 * @method CollectionContent[]    findAll()
 * @method CollectionContent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CollectionContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectionContent::class);
    }

    public function countCardsInExtFromUserCollection($username) {
        return $this->createQueryBuilder('cc')
            ->select('count(cards) as cards_nmb', 'card_set.name')
            ->leftJoin('cc.fromCollection', 'from_collection')
            ->leftJoin('from_collection.users', 'users')
            ->leftJoin('cc.cards', 'cards')
            ->leftJoin('cards.cardSet', 'card_set')
            ->where('users.username = :username')
            ->setParameter('username', $username)
            ->groupBy('cards.cardSet')
            ->getQuery()
            ->getArrayResult();
    }

    public function findByUserAndSetName($user_id, $setname) {
        return $this->createQueryBuilder('cc')
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
    }

    // /**
    //  * @return CollectionContent[] Returns an array of CollectionContent objects
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
    public function findOneBySomeField($value): ?CollectionContent
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
