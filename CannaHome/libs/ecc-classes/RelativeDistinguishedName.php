<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class RelativeDistinguishedName extends Set
{
    /**
     * @param FG\ASN1\Universal\ObjectIdentifier|string $objIdentifierString
     * @param FG\ASN1\Object $value
     */
    public function __construct($objIdentifierString, object $value)
    {
        // TODO: This does only support one element in the RelativeDistinguishedName Set but it it is defined as follows:
        // RelativeDistinguishedName ::= SET SIZE (1..MAX) OF AttributeTypeAndValue
        parent::__construct(new AttributeTypeAndValue($objIdentifierString, $value));
    }

    public function getContent()
    {
        /** @var FG\ASN1\Object $firstObject */
        $firstObject = $this->children[0];

        return $firstObject->__toString();
    }

    /**
     * At the current version this code can not work since the implementation of Construct requires
     * the class to support a constructor without arguments.
     *
     * @deprecated this function is not yet implemented! Feel free to submit a pull request on github
     *
     * @param string $binaryData
     * @param int $offsetIndex
     */
    public static function fromBinary(&$binaryData, &$offsetIndex = 0): void
    {
        throw new NotImplementedException();
    }
}
