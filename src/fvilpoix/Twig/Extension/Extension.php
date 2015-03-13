<?php

namespace fvilpoix\Twig\Extension;

use Twig_Extension;

class Extension extends Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'static' => new \Twig_Function_Method($this, 'staticCall'),
        );
    }

    /**
     * @param string $class
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function staticCall($class, $method, array $arguments = [])
    {
        return call_user_func_array([$class, $method], $arguments);
    }

    public function getName()
    {
        return 'twig_fvilpoix';
    }
}
