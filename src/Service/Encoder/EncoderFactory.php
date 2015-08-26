<?php

namespace Teampass\Api\Service\Encoder;

class EncoderFactory
{
    public static function getEncoder($encoder, $key)
    {
        $class = __NAMESPACE__.'\\'.ucwords($encoder).'Encoder';
        if (!class_exists($class)) {
            throw new \Exception('Factory not found.');
        }

        return new $class($key);
    }
}
