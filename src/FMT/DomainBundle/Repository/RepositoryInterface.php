<?php

namespace FMT\DomainBundle\Repository;

use Doctrine\ORM\EntityManager;
use FMT\DataBundle\Entity\EntityInterface;

/**
 * Interface RepositoryInterface
 * @package FMT\DomainBundle\Repository
 */
interface RepositoryInterface
{
    /**
     * @param $id
     * @return EntityInterface|null
     */
    public function findById($id);

    /**
     * @param [] $ids
     * @return EntityInterface[]
     */
    public function findAllById($ids);

    /**
     * @return mixed
     */
    public function findAll();

    /**
     * @param EntityInterface $object
     */
    public function add(EntityInterface $object);

    /**
     * @param EntityInterface $object
     */
    public function remove(EntityInterface $object);

    /**
     * @return integer
     */
    public function total();

    /**
     * @param EntityInterface|null $object
     */
    public function save(EntityInterface $object = null);

    /**
     * @return mixed
     */
    public function ping();

    /**
     * @return EntityManager
     */
    public function getEm();

    /**
     * Start a local transaction
     */
    public function beginTransaction();

    /**
     * Commits the database transaction
     */
    public function commit();

    /**
     * Rolls back a transaction from a pending state
     */
    public function rollback();

    /**
     * Merges the state of a detached entity into the persistence context of this EntityManager
     *
     * @param EntityInterface $entity
     * @return EntityInterface|object|null
     */
    public function merge(EntityInterface $entity = null);
}
