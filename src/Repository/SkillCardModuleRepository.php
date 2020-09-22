<?php

namespace App\Repository;

use App\Entity\Module;
use App\Entity\SkillCard;
use App\Entity\SkillCardModule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SkillCardModule|null find($id, $lockMode = null, $lockVersion = null)
 * @method SkillCardModule|null findOneBy(array $criteria, array $orderBy = null)
 * @method SkillCardModule[]    findAll()
 * @method SkillCardModule[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SkillCardModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SkillCardModule::class);
    }

    public function getBySkillCardAndModule(SkillCard $skillCard, Module $module)
    {
        return $this->createQueryBuilder('sm')
            ->where('sm.module = :mod')
            ->andWhere('sm.skillcard = :s')
            ->setParameters([
                'mod' => $module->getId(),
                'sm' => $skillCard->getId()
            ])->getQuery()->getFirstResult();
    }

    // /**
    //  * @return SkillCardModule[] Returns an array of SkillCardModule objects
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
    public function findOneBySomeField($value): ?SkillCardModule
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
