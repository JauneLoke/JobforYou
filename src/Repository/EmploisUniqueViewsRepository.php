<?php

namespace App\Repository;

use App\Entity\EmploisUniqueViews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EmploisUniqueViews|null find($id, $lockMode = null, $lockVersion = null)
 * @method EmploisUniqueViews|null findOneBy(array $criteria, array $orderBy = null)
 * @method EmploisUniqueViews[]    findAll()
 * @method EmploisUniqueViews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmploisUniqueViewsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EmploisUniqueViews::class);
    }

    // /**
    //  * @return EmploisUniqueViews[] Returns an array of EmploisUniqueViews objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EmploisUniqueViews
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
