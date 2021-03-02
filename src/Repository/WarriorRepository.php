<?php

namespace App\Repository;

use App\Entity\Warrior;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Warrior|null find($id, $lockMode = null, $lockVersion = null)
 * @method Warrior|null findOneBy(array $criteria, array $orderBy = null)
 * @method Warrior[]    findAll()
 * @method Warrior[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WarriorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Warrior::class);
    }

    // /**
    //  * @return Warrior[] Returns an array of Warrior objects
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
    public function findOneBySomeField($value): ?Warrior
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
