<?php

namespace fvilpoix\Symfony\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Carbon\Carbon;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\EventDispatcher\Event;
use fvilpoix\Symfony\Command\Events\Event as CommandEvent;
use fvilpoix\Symfony\Command\Events\Events as CommandEvents;

abstract class AbstractCommand extends ContainerAwareCommand
{
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $env = $input->getOption('env');

        // -e=prod => $env = '=prod'
        if (!$input->hasParameterOption(array('--no-interaction', '-n'))
                && ($env == 'prod' || $env == '=prod')) {
            $dialog = $this->getHelperSet()->get('dialog');

            if (!$dialog->askConfirmation(
                $output,
                '<question>Do command on env <error>prod</error> ?</question>',
                false
            )) {
                $output->writeln('<comment>Command interupted by user</comment>');
                exit;
            }
        }
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $start = new \DateTime();
        $output->writeln('<comment>'.$this->getName().'</comment> started at <info>'.$start->format('Y-m-d H:i:s').'</info>');

        $msg = '';

        try {
            $return = parent::run($input, $output);
        } catch (\Exception $ex) {
            $msg = '<error>FAILURE of </error>';
            $this->onError($input, $output, $ex);
        }

        $end = new \DateTime();

        $duration = Carbon::instance($start)->diffInSeconds(Carbon::instance($end));
        $msg .= '<comment>'.$this->getName().'</comment> ended at <info>'.$end->format('Y-m-d H:i:s').'</info>. Duration is <info>'.$duration.'</info> seconds.';

        $this->displayMemory($output);
        $output->writeln($msg);

        if (isset($ex)) {
            $this->dispatch(CommandEvents::ERROR, new CommandEvent($this, array('exception' => $ex)));
            throw $ex;
        }

        $this->dispatch(CommandEvents::TERMINATE, new CommandEvent($this));

        return $return;
    }

    protected function displayMemory(OutputInterface $output)
    {
        $mega = 1048576;
        $memory = round((((float) memory_get_usage(true)) / $mega));
        $memory_peak = round((((float) memory_get_peak_usage(true)) / $mega));

        $output->writeln("Memory: <comment>$memory</comment> / Peak: <comment>$memory_peak</comment> (mo)");
    }

    protected function onError(InputInterface $input, OutputInterface $output, \Exception $e)
    {
        // to override
    }

    protected function getKernel()
    {
        return $this->getApplication()->getKernel();
    }

    protected function dispatch($eventName, Event $event)
    {
        $this->getContainer()->get('event_dispatcher')->dispatch($eventName, $event);
    }

    protected function runCommand(InputInterface $input, OutputInterface $output, $commandString, array $options = array(), $quiet = true)
    {
        $defaults = [
            '-e' => $input->getOption('env'),
            'command' => $commandString,
        ];

        if (!$input->isInteractive()) {
            $defaults['--no-interaction'] = true;
        }

        $command = $this->getApplication()->find($command);

        if ($quiet) {
            $oldVerbosity = $output->getVerbosity();
            $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        $result = $command->run(new ArrayInput(array_merge($defaults, $options)), $output);

        if ($quiet) {
            $output->setVerbosity($oldVerbosity);
        }

        return $result;
    }
}
