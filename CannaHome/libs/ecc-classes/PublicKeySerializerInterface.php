<?php

interface PublicKeySerializerInterface
{
    /**
     * @return string
     */
    public function serialize(PublicKeyInterface $key);

    /**
     * @param  string $formattedKey
     *
     * @return PublicKeyInterface
     */
    public function parse($formattedKey);
}
