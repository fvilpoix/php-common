<?php

namespace fvilpoix\Symfony\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use fvilpoix\Symfony\Container\ShortcutedContainerTrait;

abstract class Manager
{
    use ShortcutedContainerTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function get($service)
    {
        return $this->getContainer()->get($service);
    }

    /**
     * Persist object in database.
     *
     * @param object  $entity
     * @param boolean $flush
     */
    public function saveEntity($entity, $flush = true)
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Persist multiple object.
     *
     * @param $entities
     * @param bool $flush
     */
    public function saveEntities($entities, $flush = true)
    {
        foreach ($entities as $entity) {
            $this->getEntityManager()->persist($entity);
        }
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove multiple object.
     *
     * @param $entities
     * @param bool $flush
     */
    public function removeEntities($entities, $flush = true)
    {
        foreach ($entities as $entity) {
            $this->getEntityManager()->remove($entity);
        }
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Delete object in database.
     *
     * @param object $entity
     */
    public function removeEntity($entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
}
