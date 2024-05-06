<?php
/*
 * This file is part of the PHPASN1 library.
 *
 * Copyright © Friedrich Große <friedrich.grosse@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TemplateParser
{
    /**
     * @param string $data
     *
     * @return FG\ASN1\Object|Sequence
     */
    public function parseBase64($data, array $template)
    {
        // TODO test with invalid data
        return $this->parseBinary(base64_decode($data), $template);
    }

    /**
     * @param string $binary
     *
     * @return FG\ASN1\Object|Sequence
     */
    public function parseBinary($binary, array $template)
    {
        $parsedObject = Object::fromBinary($binary);

        foreach ($template as $key => $value) {
            $this->validate($parsedObject, $key, $value);
        }

        return $parsedObject;
    }

    private function validate(object $object, $key, $value): void
    {
        if (is_array($value)) {
            $this->assertTypeId($key, $object);

            // @var Construct $object
            foreach ($value as $key => $child) {
                $this->validate($object->current(), $key, $child);
                $object->next();
            }
        } else {
            $this->assertTypeId($value, $object);
        }
    }

    private function assertTypeId($expectedTypeId, object $object): void
    {
        $actualType = $object->getType();
        if ($expectedTypeId != $actualType) {
            throw new Exception("Expected type ($expectedTypeId) does not match actual type ($actualType");
        }
    }
}
