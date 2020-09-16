<?php

namespace App\Repository;

use App\Entity\Certification;
use App\Entity\CertificationModule;
use App\Entity\Module;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Module::class);
    }

    /**
     * Return all not mandatory modules of given Certification
     * @param Certification $certification
     * @return Module[]
     */
    public function findNotMandatoryModules(Certification $certification)
    {
        $qb = $this->createQueryBuilder('m')
            ->innerJoin(CertificationModule::class, 'cm', Join::WITH, 'm.id = cm.module')
            ->innerJoin(Certification::class, 'c', Join::WITH, 'c.id = cm.certification')
            ->where('c.id = :id')
            ->andWhere('cm.isMandatory = FALSE')
            ->setParameter('id', $certification->getId())->getQuery();
        return $qb->getResult();
    }

    // /**
    //  * @return Module[] Returns an array of Module objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Module
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
