<?php

namespace FMT\DataBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use FMT\DataBundle\Doctrine\Extensions\DBAL\Types\AbstractDateTimeType;
use FMT\DataBundle\Entity\EntityInterface;
use FMT\DomainBundle\Repository\RepositoryInterface;

/**
 * Class DoctrineRepository
 * @package FMT\DataBundle\Repository
 */
class DoctrineRepository extends EntityRepository implements RepositoryInterface
{
    protected $relations = [];

    /**
     * @param $id
     * @return null|EntityInterface
     */
    public function findById($id)
    {
        return $this->find($id);
    }

    /**
     * @param $ids
     * @return array
     */
    public function findAllById($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        $builder = $this->createQueryBuilder('e');
        $builder->andWhere($builder->expr()->in('e.id', $ids));

        return $builder->getQuery()->getResult();
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * @param EntityInterface $object
     * @return mixed|void
     */
    public function add(EntityInterface $object)
    {
        $this->getEntityManager()->persist($object);
    }

    /**
     * @param EntityInterface $object
     * @return mixed|void
     */
    public function remove(EntityInterface $object)
    {
        $this->getEntityManager()->remove($object);
    }

    /**
     * @return mixed
     */
    public function total()
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        $builder->select('COUNT(e)');
        $builder->from($this->getEntityName(), 'e');

        return $builder->getQuery()->getSingleScalarResult();
    }

    /**
     * @param EntityInterface|null $object
     * @return null|EntityInterface
     */
    public function save(EntityInterface $object = null)
    {
        if ($object && !$object->getId()) {
            $this->add($object);
        }

        $manager = $this->getEntityManager();
        $manager->flush($object);

        return $object;
    }

    /**
     * Renew database connection if dropped
     */
    public function ping()
    {
        if ($this->_em->getConnection()->ping() === false) {
            $this->_em->getConnection()->close();
            $this->_em->getConnection()->connect();
        }
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEm()
    {
        return $this->getEntityManager();
    }

    /**
     * Start a local transaction
     */
    public function beginTransaction()
    {
        $this->getEntityManager()->beginTransaction();
    }

    /**
     * Commits the database transaction
     */
    public function commit()
    {
        $this->getEntityManager()->getConnection()->commit();
    }

    /**
     * Rolls back a transaction from a pending state
     */
    public function rollback()
    {
        if ($this->getEntityManager()->getConnection()->isTransactionActive()) {
            $this->getEntityManager()->rollback();
        }
    }

    /**
     * Returns repository by entity class name or object
     *
     * @param mixed $class
     * @return DoctrineRepository
     */
    protected function getRepository($class)
    {
        if ($class instanceof EntityInterface) {
            $class = get_class($class);
        }

        return $this->getEm()->getRepository((string)$class);
    }

    /**
     * Creates query builder of specific repository
     *
     * @param mixed $class
     * @param string $alias
     * @param string $indexBy The index for the from.
     * @return QueryBuilder
     */
    protected function createQueryBuilderOf($class, $alias, $indexBy = null)
    {
        return $this->getRepository($class)->createQueryBuilder($alias, $indexBy);
    }

    /**
     * Method adds related objects to the query
     *
     * @param QueryBuilder $builder
     * @param string $with
     * @param bool $leftJoin
     * @return QueryBuilder
     */
    protected function joinRelated(QueryBuilder $builder, $with, $leftJoin = false)
    {
        $tables = $this->relations[$with];
        array_map(function ($alias) use ($tables, &$builder, $leftJoin) {
            if ($leftJoin) {
                $builder->leftJoin($tables[$alias], $alias);
            } else {
                $builder->join($tables[$alias], $alias);
            }
            $builder->addSelect($alias);
        }, array_keys($tables));

        return $builder;
    }

    /**
     * Merges the state of a detached entity into the persistence context of this EntityManager
     *
     * @param EntityInterface $entity
     * @return EntityInterface|object|null
     */
    public function merge(EntityInterface $entity = null)
    {
        return $entity ? $this->getEntityManager()->merge($entity) : null;
    }

    /**
     * @param $date
     * @return \DateTime|null
     */
    protected function convertStringToUtcDateTime($date)
    {
        if (is_string($date)) {
            $utcDate = new \DateTime();
            $timeZone = new \DateTimeZone(AbstractDateTimeType::DEFAULT_TIME_ZONE);
            $utcDate->setTimezone($timeZone);
            $utcDate->modify($date);

            return $utcDate;
        }

        return null;
    }
}
