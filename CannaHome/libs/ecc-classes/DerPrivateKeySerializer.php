<?php

/**
 * PEM Private key formatter.
 *
 * @see https://tools.ietf.org/html/rfc5915
 */
class DerPrivateKeySerializer implements PrivateKeySerializerInterface
{
    public const VERSION = 1;

    /**
     * @var null|MathAdapterInterface|Mdanter\Ecc\Math\DebugDecorator
     */
    private $adapter;

    /**
     * @var DerPublicKeySerializer
     */
    private $pubKeySerializer;

    public function __construct(?MathAdapterInterface $adapter = null, ?PemPublicKeySerializer $pubKeySerializer = null)
    {
        $this->adapter = $adapter ?: MathAdapterFactory::getAdapter();
        $this->pubKeySerializer = $pubKeySerializer ?: new DerPublicKeySerializer($this->adapter);
    }

    /**
     * {@inheritDoc}
     *
     * @see \Mdanter\Ecc\Serializer\PrivateKeySerializerInterface::serialize()
     */
    public function serialize(PrivateKeyInterface $key)
    {
        $privateKeyInfo = new Sequence(
            new Integer(self::VERSION),
            new OctetString($this->formatKey($key)),
            new ExplicitlyTaggedObject(0, CurveOidMapper::getCurveOid($key->getPoint()->getCurve())),
            new ExplicitlyTaggedObject(1, $this->encodePubKey($key))
        );

        return $privateKeyInfo->getBinary();
    }

    /**
     * @return BitString
     */
    private function encodePubKey(PrivateKeyInterface $key)
    {
        return new BitString(
            $this->pubKeySerializer->getUncompressedKey($key->getPublicKey())
        );
    }

    /**
     * @return int|mixed|string
     */
    private function formatKey(PrivateKeyInterface $key)
    {
        return $this->adapter->decHex($key->getSecret());
    }

    /**
     * @param string $data
     * {@inheritDoc}
     *
     * @see \Mdanter\Ecc\Serializer\PrivateKeySerializerInterface::parse()
     */
    public function parse($data)
    {
        $asnObject = Object::fromBinary($data);

        if (!($asnObject instanceof Sequence) || $asnObject->getNumberofChildren() !== 4) {
            throw new RuntimeException('Invalid data.');
        }

        $children = $asnObject->getChildren();

        $version = $children[0];

        if ($version->getContent() != 1) {
            throw new RuntimeException('Invalid data: only version 1 (RFC5915) keys are supported.');
        }

        $key = $this->adapter->hexDec($children[1]->getContent());
        $oid = $children[2]->getContent();

        $generator = CurveOidMapper::getGeneratorFromOid($oid);

        return $generator->getPrivateKeyFrom($key);
    }
}
