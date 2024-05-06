<?php

class Formatter
{
    /**
     * @return Sequence
     */
    public function toAsn(SignatureInterface $signature)
    {
        return new Sequence(
            new BignumInt($signature->getR()),
            new BignumInt($signature->getS())
        );
    }

    /**
     * @return string
     */
    public function serialize(SignatureInterface $signature)
    {
        return $this->toAsn($signature)->getBinary();
    }
}
