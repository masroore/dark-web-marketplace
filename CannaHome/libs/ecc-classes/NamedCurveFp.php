<?php

class NamedCurveFp extends CurveFp
{
    /**
     * @var int|string
     */
    private $name;

    /**
     * @param int|string           $name
     */
    public function __construct($name, CurveParameters $parameters, MathAdapterInterface $adapter)
    {
        $this->name = $name;

        parent::__construct($parameters, $adapter);
    }

    /**
     * @return int|string
     */
    public function getName()
    {
        return $this->name;
    }
}
