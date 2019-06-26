<?php

namespace App\Repository;

use App\Entity\Entreprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Entreprise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entreprise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entreprise[]    findAll()
 * @method Entreprise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntrepriseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Entreprise::class);
    }

    public function countReservation()
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e)')
            ->andWhere('e.reservationOk IS NULL OR e.reservationOk = 0')
            ->join('e.User', 'u')
            ->andWhere('u.enabled = false')
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Entreprise[] Returns an array of Entreprise objects
    //  */
    public function findReservation($orders, $nbPerPage, $offset)
    {
        $query = $this->createQueryBuilder('e')
            ->setMaxResults((int)$nbPerPage)
            ->setFirstResult((int)$offset)
            ->andWhere('e.reservationOk IS NULL OR e.reservationOk = 0')
            ->join('e.User', 'u')
            ->andWhere('u.enabled = false');

        foreach ($orders as $key => $order) {
            $query->addOrderBy('e.'.$key, $order);
        }

        return $query
        ->getQuery()
        ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Entreprise
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
