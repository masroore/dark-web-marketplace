<?php

class ModularArithmetic
{
    /**
     * @var MathAdapterInterface
     */
    private $adapter;

    private $modulus;

    public function __construct(MathAdapterInterface $adapter, $modulus)
    {
        $this->adapter = $adapter;
        $this->modulus = $modulus;
    }

    /**
     * @return int|string
     */
    public function add($augend, $addend)
    {
        return $this->adapter->mod($this->adapter->add($augend, $addend), $this->modulus);
    }

    /**
     * @return int|string
     */
    public function sub($minuend, $subtrahend)
    {
        return $this->adapter->mod($this->adapter->sub($minuend, $subtrahend), $this->modulus);
    }

    /**
     * @return int|string
     */
    public function mul($multiplier, $muliplicand)
    {
        return $this->adapter->mod($this->adapter->mul($multiplier, $muliplicand), $this->modulus);
    }

    /**
     * @return int|string
     */
    public function div($dividend, $divisor)
    {
        return $this->adapter->mod($this->adapter->mul($dividend, $this->adapter->inverseMod($divisor, $this->modulus)), $this->modulus);
    }

    /**
     * @return mixed
     */
    public function pow($base, $exponent)
    {
        return $this->adapter->powmod($base, $exponent, $this->modulus);
    }
}
