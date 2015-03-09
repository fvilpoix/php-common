<?php

namespace fvilpoix\Symfony\Bundle\ToolsBundle\Command;

use fvilpoix\Symfony\Command\AbstractCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\DBALException;

abstract class AbstractDoctrineCommand extends AbstractCommand
{
    protected function generateSchema()
    {
        $this->getDoctrineTools()->generateSchema();
    }

    protected function disableDatabaseForeignKeyChecks(OutputInterface $output, $continueOnError = true)
    {
        try {
            $this->getDoctrineTools()->disableDatabaseForeignKeyChecks();
        } catch (DBALException $e) {
            $output->writeln('<error>Error disabling foreign_keys : '.$e->getMessage().'</error>');

            if (!$continueOnError) {
                throw $e;
            }
        }
    }

    protected function enableDatabaseForeignKeyChecks(OutputInterface $output, $continueOnError = true)
    {
        try {
            $this->getDoctrineTools()->enableDatabaseForeignKeyChecks();
        } catch (DBALException $e) {
            $output->writeln('<error>Error enabling foreign_keys : '.$e->getMessage().'</error>');

            if (!$continueOnError) {
                throw $e;
            }
        }
    }

    protected function emptyDatabase()
    {
        $this->getDoctrineTools()->truncateAll();
    }

    /**
     * @return \fvilpoix\Symfony\Bundle\ToolsBundle\Doctrine\DatabaseTools
     */
    protected function getDoctrineTools()
    {
        return $this->getContainer()->get('fvilpoix.tools.doctrine.tools');
    }
}
