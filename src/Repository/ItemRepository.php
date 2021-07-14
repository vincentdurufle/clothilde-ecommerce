<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    /**
     * @return int|mixed|string
     */
    public function findByEnabledAndOrdered(): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.disabled = false')
            ->orderBy('i.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @param $value
     * @return array
     */
    public function findByTypeOrdered($value): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.type = :val')
            ->andWhere('i.disabled = false')
            ->orderBy('i.createdAt', 'DESC')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }
}
