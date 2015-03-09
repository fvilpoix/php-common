<?php

namespace fvilpoix\Symfony\Command\Events;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use fvilpoix\Symfony\Command\AbstractCommand;

class Event extends BaseEvent
{
    /**
     * @var \fvilpoix\Symfony\Bundle\ToolsBundle\Command\AbstractCommand
     */
    protected $command;

    protected $parameters;

    public function __construct(AbstractCommand $command, array $parameters = array())
    {
        $this->command = $command;
        $this->parameters = $parameters;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
