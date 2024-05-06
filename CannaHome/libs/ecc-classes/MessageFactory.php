<?php

class MessageFactory
{
    /**
     * @var MathAdapterInterface
     */
    private $adapter;

    public function __construct(MathAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @return Message
     */
    public function plaintext($content, $algo)
    {
        return new Message($this->adapter, $content, $algo);
    }

    /**
     * @return EncryptedMessage
     */
    public function ciphertext($content)
    {
        return new EncryptedMessage($content);
    }
}
