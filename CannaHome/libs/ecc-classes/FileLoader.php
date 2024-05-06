<?php

interface FileLoader
{
    /**
     * @return PublicKeyInterface
     */
    public function loadPublicKeyData($file);

    /**
     * @return PrivateKeyInterface
     */
    public function loadPrivateKeyData($file);
}
