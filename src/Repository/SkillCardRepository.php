<?php

namespace App\Repository;

use App\Entity\Certification;
use App\Entity\SkillCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SkillCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method SkillCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method SkillCard[]    findAll()
 * @method SkillCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SkillCard::class);
    }

    /**
     * @return SkillCard[]
     */
    public function findByCertification(Certification $certification)
    {
        return $this->createQueryBuilder('s')
            ->where('s.certification = :val')
            ->setParameter('val', $certification->getId())
            ->getQuery()->getResult();
    }

    // /**
    //  * @return SkillCard[] Returns an array of SkillCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SkillCard
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
