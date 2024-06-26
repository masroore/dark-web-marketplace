<?php

/**
 * @see https://tools.ietf.org/html/rfc5480#page-3
 */
class DerPublicKeySerializer implements PublicKeySerializerInterface
{
    public const X509_ECDSA_OID = '1.2.840.10045.2.1';

    /**
     * @var MathAdapterInterface
     */
    private $adapter;

    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @var Parser
     */
    private $parser;

    public function __construct(?MathAdapterInterface $adapter = null)
    {
        $this->adapter = $adapter ?: MathAdapterFactory::getAdapter();

        $this->formatter = new Formatter($this->adapter);
        $this->parser = new Parser($this->adapter);
    }

    /**
     * @return string
     */
    public function serialize(PublicKeyInterface $key)
    {
        return $this->formatter->format($key);
    }

    public function getUncompressedKey(PublicKeyInterface $key)
    {
        return $this->formatter->encodePoint($key->getPoint());
    }

    /**
     * {@inheritDoc}
     *
     * @see \Mdanter\Ecc\Serializer\PublicKey\PublicKeySerializerInterface::parse()
     */
    public function parse($string)
    {
        return $this->parser->parse($string);
    }
}
