<?php

namespace App\Repository;

use App\Entity\CertificationModule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CertificationModule|null find($id, $lockMode = null, $lockVersion = null)
 * @method CertificationModule|null findOneBy(array $criteria, array $orderBy = null)
 * @method CertificationModule[]    findAll()
 * @method CertificationModule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CertificationModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CertificationModule::class);
    }
    
    // /**
    //  * @return CertificationModule[] Returns an array of CertificationModule objects
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
    public function findOneBySomeField($value): ?CertificationModule
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
