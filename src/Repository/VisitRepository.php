<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Visit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Visit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Visit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Visit[]    findAll()
 * @method Visit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VisitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Visit::class);
    }

    /**
     * @return Visit[]
     */
    public function getAllPurchasesWinners(array $types = null): array
    {
        $entityManager = $this->getEntityManager();
        $expr = $entityManager->getExpressionBuilder();

        $inQueryBuilder = $entityManager->createQueryBuilder()
            ->select('visit2.clientId')
            ->from(Visit::class, 'visit2')
            ->where('visit2.isCheckout = :checkout')
        ;
        $types = null === $types ? [Visit::TYPE_OURS] : $types;

        $resultQueryBuilder = $this->createQueryBuilder('visit')
            ->where(
                $expr->in(
                    'visit.clientId',
                    $inQueryBuilder->getDQL()
                )
            )
            ->andWhere(
                $expr->in(
                    'visit.type',
                    $types
                )
            )
            ->setMaxResults(1)
            ->orderBy('visit.date', 'DESC')
            ->setParameter('checkout', true)
        ;

        return $resultQueryBuilder
            ->getQuery()
            ->getResult();
    }
}
