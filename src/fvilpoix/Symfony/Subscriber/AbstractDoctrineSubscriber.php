<?php

namespace fvilpoix\Symfony\Subscriber;

use Symfony\Component\DependencyInjection\ContainerInterface;
use fvilpoix\Symfony\Container\ShortcutedContainerTrait;

abstract class AbstractDoctrineSubscriber implements \Doctrine\Common\EventSubscriber
{
    use ShortcutedContainerTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function get($service)
    {
        return $this->container->get($service);
    }
}
