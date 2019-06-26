<?php

namespace App\Repository;

use App\Entity\TypeExperience;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TypeExperience|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeExperience|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeExperience[]    findAll()
 * @method TypeExperience[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeExperienceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TypeExperience::class);
    }

    // /**
    //  * @return TypeExperience[] Returns an array of TypeExperience objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TypeExperience
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
