<?php

namespace fvilpoix\Symfony\Doctrine\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class SingleColumnHydrator extends AbstractHydrator
{
    const HYDRATE_SINGLE_COLUMN = 'HYDRATE_SINGLE_COLUMN';

    /**
     * {@inheritdoc}
     */
    protected function hydrateAllData()
    {
        $result = array();

        while ($data = $this->_stmt->fetch(\PDO::FETCH_ASSOC)) {
            $this->hydrateRowData($data, $result);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateRowData(array $data, array &$cache, array &$result)
    {
        $value = array_shift($data);
        $result[$value] = $value;
    }
}
