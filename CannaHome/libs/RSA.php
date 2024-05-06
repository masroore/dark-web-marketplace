<?php

require_once 'Crypt_RSA.php';

class RSA
{
    private $private_key = false;

    public function __construct($private_key = false)
    {
        $this->private_key = $private_key ?: Session::get('private_key');
    }

    public function qEncrypt($content, $public_key = false)
    {
        $rsa = new Crypt_RSA();
        $rsa->setHash('sha256');
        $rsa->setMGFHash('sha256');
        if (!$public_key && $this->private_key) {
            $rsa->loadKey($this->private_key);
            $public_key = $rsa->getPublicKey();
        } elseif (!$this->private_key) {
            return false; // No public key
        }
        $rsa->loadKey($public_key);

        return $rsa->encrypt($content);
    }

    public function qDecrypt($ciphertext)
    {
        if ($this->private_key) {
            $rsa = new Crypt_RSA();
            $rsa->setHash('sha256');
            $rsa->setMGFHash('sha256');
            $rsa->loadKey($this->private_key);

            return $rsa->decrypt($ciphertext);
        }

        return false;

    }

    public function qSign($content)
    {
        if ($this->private_key) {
            $rsa = new Crypt_RSA();
            $rsa->setHash('sha256');
            $rsa->setMGFHash('sha256');
            $rsa->loadKey($this->private_key);

            return $rsa->sign($content);
        }

        return false;

    }

    public function qVerify($content, $signature, $public_key = false)
    {
        $rsa = new Crypt_RSA();
        $rsa->setHash('sha256');
        $rsa->setMGFHash('sha256');
        if (!$public_key && $this->private_key) {
            $rsa->loadKey($this->private_key);
            $public_key = $rsa->getPublicKey();
        } elseif (!$this->private_key) {
            return false; // No public key
        }
        $rsa->loadKey($public_key);

        return $rsa->verify($content, $signature);
    }
}
