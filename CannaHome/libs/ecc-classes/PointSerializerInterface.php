<?php

interface PointSerializerInterface
{
    /**
     * @return string
     */
    public function serialize(PointInterface $point);

    /**
     * @param  CurveFpInterface $curve  Curve that contains the serialized point
     * @param  string           $string
     *
     * @return PointInterface
     */
    public function unserialize(CurveFpInterface $curve, $string);
}
