<?php

namespace App\Repository;

use App\Entity\Emplois;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Emplois|null find($id, $lockMode = null, $lockVersion = null)
 * @method Emplois|null findOneBy(array $criteria, array $orderBy = null)
 * @method Emplois[]    findAll()
 * @method Emplois[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmploisRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Emplois::class);
    }

    public function countEmplois($search, $filters)
    {
        $query = $this->createQueryBuilder('e')
            ->select('COUNT(e)')
            ->andWhere('e.active = 1');

        if(!empty($search)) {
            $words = preg_split('/\s+/', $search);
            foreach ($words as $k => $word) {
                if(!in_array($word, array('de', 'du', 'des', 'le', 'la', 'les', 'et', 'en', '/'))) {
                    $words[$k] = '+'.$word.'* ';
                }
            }
            $against = rtrim(implode(' ', $words));

            $query
                ->andWhere('match (e.intitule) against (:search boolean) > 0')
                ->setParameter('search', $against)
                ->addOrderBy('match (e.intitule) against (:search boolean)', 'DESC');
        }

        if(count($filters)) {
            foreach ($filters as $filterType => $f) {
                foreach ($f as $filter) {
                    $query
                        ->join('e.'.$filterType, $filterType)
                        ->andWhere($filterType.'.id = :id'.$filterType.$filter)
                        ->setParameter('id'.$filterType.$filter, $filter);
                }
            }
        }

        return $query
            ->getQuery()
            ->getSingleScalarResult();
    }

    // /**
    //  * @return Emplois[] Returns an array of Emplois objects
    //  */
    public function findEmplois($search, $orders, $filters, $nbPerPage, $offset)
    {
        $query = $this->createQueryBuilder('e')
            ->setMaxResults((int)$nbPerPage)
            ->setFirstResult((int)$offset)
            ->andWhere('e.active = 1');

        if(!empty($search)) {
            $words = preg_split('/\s+/', $search);
            foreach ($words as $k => $word) {
                if(!in_array($word, array('de', 'du', 'des', 'le', 'la', 'les', 'et', 'en', '/'))) {
                    $words[$k] = '+'.$word.'* ';
                }
            }
            $against = rtrim(implode(' ', $words));

            $query
                ->andWhere('match (e.intitule) against (:search boolean) > 0')
                ->setParameter('search', $against)
                ->addOrderBy('match (e.intitule) against (:search boolean)', 'DESC');
        }

        if(count($filters)) {
            foreach ($filters as $filterType => $f) {
                foreach ($f as $filter) {
                    $query
                        ->join('e.'.$filterType, $filterType)
                        ->andWhere($filterType.'.id = :id'.$filterType.$filter)
                        ->setParameter('id'.$filterType.$filter, $filter);
                }
            }
        }

        foreach ($orders as $order) {
            $query->addOrderBy('e.'.$order, 'DESC');
        }

        return $query
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Emplois[] Returns an array of Emplois objects
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
    public function findOneBySomeField($value): ?Emplois
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
