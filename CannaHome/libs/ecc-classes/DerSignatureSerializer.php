<?php

class DerSignatureSerializer
{
    /**
     * @var Der\Parser
     */
    private $parser;

    /**
     * @var Der\Formatter
     */
    private $formatter;

    public function __construct()
    {
        $this->parser = new Der\Parser();
        $this->formatter = new Der\Formatter();
    }

    /**
     * @return string
     */
    public function serialize(SignatureInterface $signature)
    {
        return $this->formatter->serialize($signature);
    }

    /**
     * @param string $binary
     *
     * @return Signature
     */
    public function parse($binary)
    {
        return $this->parser->parse($binary);
    }
}
