<?php

declare(strict_types=1);

namespace FMT\DataBundle\Traits;

use Doctrine\ORM\QueryBuilder;

trait ChunkedQueryBuilder
{
    /**
     * @param QueryBuilder $qb
     * @param int|null     $count
     * @param int          $offset
     *
     * @return QueryBuilder
     */
    private function getChunkedQueryBuilder(QueryBuilder $qb, ?int $count = null, int $offset = 0): QueryBuilder
    {
        $qb->setFirstResult($offset);

        if (!is_null($count)) {
            $qb->setMaxResults($count);
        }

        return $qb;
    }
}
