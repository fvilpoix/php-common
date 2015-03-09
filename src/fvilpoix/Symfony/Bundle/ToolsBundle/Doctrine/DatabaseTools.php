<?php

namespace fvilpoix\Symfony\Bundle\ToolsBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;

class DatabaseTools
{
    /**
     * @var Doctrine\Common\Persistence\ObjectManager
     */
    protected $om;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $truncatedTables;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->connection = $om->getConnection();
    }

    protected function reset()
    {
        $this->truncatedTables = [];
    }

    public function truncateAll()
    {
        $this->truncateMetadatas($this->om->getMetadataFactory()->getAllMetadata());
    }

    /**
     * @param array $entitiesNames list of full qualified class names
     */
    public function truncateEntities(array $entitiesNames)
    {
        $metadatas = [];
        foreach ($entitiesNames as $entity) {
            $metadatas[] = $this->om->getMetadataFactory()->getMetadataFor($entity);
        }

        $this->truncateMetadatas($metadatas);
    }

    /**
     * @param  iterable   $metadatas list of \Doctrine\ORM\Mapping\ClassMetadata
     * @throws \Exception
     */
    public function truncateMetadatas($metadatas)
    {
        $this->reset();

        $this->connection->beginTransaction();
        try {
            $this->disableDatabaseForeignKeyChecks();

            /* @var $classMetadata \Doctrine\ORM\Mapping\ClassMetadata */
            foreach ($metadatas as $classMetadata) {
                if ($classMetadata->isMappedSuperclass === false) {
                    $this->truncateTable($classMetadata->getTableName());

                    foreach ($classMetadata->getAssociationMappings() as $field) {
                        if (isset($field['joinTable']) && isset($field['joinTable']['name'])) {
                            $this->truncateTable($field['joinTable']['name']);
                        }
                    }
                }
            }

            $this->enableDatabaseForeignKeyChecks();
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollback();
            throw $e;
        }
    }

    protected function truncateTable($tableName)
    {
        if (!in_array($tableName, $this->truncatedTables)) {
            $query = $this->connection->getDatabasePlatform()->getTruncateTableSql($tableName, true);
            $this->connection->executeUpdate($query);
            $this->truncatedTables[] = $tableName;
        }
    }

    public function generateSchema()
    {
        $metadatas = $this->om->getMetadataFactory()->getAllMetadata();

        if (!empty($metadatas)) {
            $tool = new \Doctrine\ORM\Tools\SchemaTool($this->om);
            $tool->dropDatabase();
            $tool->dropSchema($metadatas);
            $tool->createSchema($metadatas);
        }
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function disableDatabaseForeignKeyChecks()
    {
        $this->connection->exec('SET foreign_key_checks = 0;');
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function enableDatabaseForeignKeyChecks()
    {
        $this->connection->exec('SET foreign_key_checks = 1;');
    }
}
