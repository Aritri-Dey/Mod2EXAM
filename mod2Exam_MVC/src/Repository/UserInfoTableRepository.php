<?php

namespace App\Repository;

use App\Entity\UserInfoTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserInfoTable>
 *
 * @method UserInfoTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInfoTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInfoTable[]    findAll()
 * @method UserInfoTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInfoTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInfoTable::class);
    }

    public function save(UserInfoTable $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserInfoTable $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
