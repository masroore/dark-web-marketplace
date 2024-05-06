<?php

interface PrivateKeySerializerInterface
{
    /**
     * @return string
     */
    public function serialize(PrivateKeyInterface $key);

    /**
     * @param  string $formattedKey
     *
     * @return PrivateKeyInterface
     */
    public function parse($formattedKey);
}
