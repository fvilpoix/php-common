<?php

namespace fvilpoix\Symfony\Container;

trait ShortcutedContainerTrait
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @return Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->getContainer()->get('event_dispatcher');
    }

    protected function dispatch($eventName, \Symfony\Component\EventDispatcher\Event $event)
    {
        $this->getEventDispatcher()->dispatch($eventName, $event);
    }

    public function getParameter($key)
    {
        return $this->getContainer()->getParameter((string) $key);
    }

    protected function log($level, $message, array $context = array())
    {
        if ($logger = $this->getContainer()->get('logger')) {
            $logger->log($level, $message, $context);
        }
    }
}
