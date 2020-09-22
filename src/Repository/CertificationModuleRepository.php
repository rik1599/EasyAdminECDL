<?php

namespace App\Repository;

use App\Entity\Certification;
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

    /**
     * Return modules of given Certification
     * @param Certification $certification
     * @param bool|null true if you are looking for mandatory modules,
     *      false if you are looking for non-mandatory modules,
     *      null if you are looking for all modules
     * @return CertificationModule[]
     */
    public function findByCertification(Certification $certification, bool $mandatory = null) {
        $qb = $this->createQueryBuilder('cm')
            ->where('cm.certification = :id');

        if (!is_null($mandatory)) {
            $mandatoryString = $mandatory ? 'TRUE' : 'FALSE';
            $qb->andWhere("cm.isMandatory = $mandatoryString");
        }

        $qb->setParameter('id', $certification->getId());
        return $qb->getQuery()->getResult();
    }
}
