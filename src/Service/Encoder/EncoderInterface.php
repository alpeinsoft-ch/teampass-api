<?php

namespace Teampass\Api\Service\Encoder;

interface EncoderInterface
{
    const ITCOUNT = 2072;

    public function encrypt($decrypted, $key = null);

    public function decrypt($encrypted, $key = null);
}
