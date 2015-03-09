<?php

namespace fvilpoix\Symfony\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use fvilpoix\Symfony\Container\ShortcutedContainerTrait;

abstract class AbstractEventSubscriber implements EventSubscriberInterface
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
