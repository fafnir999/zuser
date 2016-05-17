<?php
/**
 * Author: Yaroslav Kovalev
 * Date: 06.05.2016
 * Time: 13:02
 */

namespace ZUser\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AccountRepository extends EntityRepository
{
    public function getAccountsLimitQueryBuilder($offset = 0, $limit = 20)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->createQueryBuilder('a')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $queryBuilder;
    }
}