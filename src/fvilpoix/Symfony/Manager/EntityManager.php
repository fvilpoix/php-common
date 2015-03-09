<?php

namespace fvilpoix\Symfony\Manager;

abstract class EntityManager extends Manager
{
    /**
     * @var string
     */
    protected $baseClass;

    /**
     * @param  string                         $name
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository($name = null)
    {
        return $this->getEntityManager()->getRepository($name ?: $this->baseClass);
    }

    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    public function newEntity()
    {
        $entity = new $this->baseClass();

        return $entity;
    }
}
