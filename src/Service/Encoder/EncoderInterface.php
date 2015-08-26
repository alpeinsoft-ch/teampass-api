<?php

namespace Teampass\Api\Service\Encoder;

interface EncoderInterface
{
    public function encrypt($decrypted);

    public function decrypt($encrypted);
}
