<?php

class DebugDecorator implements RandomNumberGeneratorInterface
{
    /**
     * @var RandomNumberGeneratorInterface
     */
    private $generator;

    private $generatorName;

    public function __construct(RandomNumberGeneratorInterface $generator, $name)
    {
        $this->generator = $generator;
        $this->generatorName = $name;
    }

    /**
     * @param int|string $max
     *
     * @return mixed
     */
    public function generate($max)
    {
        echo $this->generatorName . '::rand() = ';

        $result = $this->generator->generate($max);

        echo $result . PHP_EOL;

        return $result;
    }
}
