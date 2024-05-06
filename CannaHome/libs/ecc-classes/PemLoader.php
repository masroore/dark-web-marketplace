<?php

class PemLoader implements FileLoader
{
    public const PEM_PRIVATE_KEY_HEADER = 'EC PRIVATE KEY';

    public const PEM_PUBLIC_KEY_HEADER = 'PUBLIC KEY';

    /**
     * {@inheritDoc}
     *
     * @see \Mdanter\Ecc\File\FileLoader::loadPublicKeyData()
     */
    public function loadPrivateKeyData($file)
    {
        if (!file_exists($file)) {
            throw new InvalidArgumentException('Key file not found.');
        }

        $data = file_get_contents($file);

        $privateKeyData = null;

        preg_match($this->buildPattern(self::PEM_PRIVATE_KEY_HEADER), $this->normalize($data), $privateKeyData);

        if (count($privateKeyData) > 1) {
            return $privateKeyData[1];
        }

        throw new InvalidArgumentException('No private key available in file.');
    }

    /**
     * {@inheritDoc}
     *
     * @see \Mdanter\Ecc\File\FileLoader::loadPublicKeyData()
     */
    public function loadPublicKeyData($file)
    {
        if (!file_exists($file)) {
            throw new InvalidArgumentException('Key file not found.');
        }

        $data = file_get_contents($file);

        $publicKeyData = null;

        preg_match($this->buildPattern(self::PEM_PUBLIC_KEY_HEADER), $this->normalize($data), $publicKeyData);

        if (count($publicKeyData) > 1) {
            return $publicKeyData[1];
        }

        throw new InvalidArgumentException('No public key available in file.');
    }

    /**
     * @return string
     */
    private function buildPattern($headerName)
    {
        $begin = '/\-\-\-\-\-BEGIN ' . $headerName . '\-\-\-\-\-';
        $end = '\-\-\-\-\-END ' . $headerName . '\-\-\-\-\-/im';

        $pattern = $begin . '(.*)' . $end;

        return $pattern;
    }

    /**
     * @return mixed
     */
    private function normalize($string)
    {
        $string = str_replace(PHP_EOL, '', $string);
        $string = str_replace("\n", '', $string);
        $string = str_replace("\r", '', $string);

        return $string;
    }
}
