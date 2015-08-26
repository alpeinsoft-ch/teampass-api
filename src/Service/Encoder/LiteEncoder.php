<?php

namespace Teampass\Api\Service\Encoder;

class LiteEncoder implements EncoderInterface
{
    /**
     * @param string $decrypted
     *
     * @return bool|string
     */
    public function encrypt($decrypted)
    {
        return base64_encode($decrypted);
    }

    /**
     * @param string $encrypted
     *
     * @return bool|string
     */
    public function decrypt($encrypted)
    {
        return base64_decode($encrypted);
    }
}
