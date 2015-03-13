<?php
namespace fvilpoix\Symfony\Doctrine\Hydrator;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use Doctrine\ORM\Internal\Hydration\HydrationException;

/**
 * Description of KeyValueHydrator.
 *
 * Hydrates a pdo 2 columns resultset, using first column as keys, and second
 * as value
 * array(
 *     rows[0][0] => rows[0][1]
 *     rows[1][0] => rows[1][1]
 *     ...
 * )
 */
class KeyValueHydrator extends AbstractHydrator
{
    const HYDRATE_KEY_VALUE = 'HYDRATE_KEY_VALUE';

    /**
     * {@inheritdoc}
     */
    protected function hydrateAllData()
    {
        $result = array();
        $cache  = array();

        $valid = false;

        while ($data = $this->_stmt->fetch(\PDO::FETCH_NUM)) {
            if (!$valid) {
                if (count($data) < 2) {
                    throw new HydrationException();
                }
                $valid = true;
            }
            $this->hydrateRowData($data, $cache, $result);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateRowData(array $data, array &$cache, array &$result)
    {
        $result[$data[0]] = $data[1];
    }
}
