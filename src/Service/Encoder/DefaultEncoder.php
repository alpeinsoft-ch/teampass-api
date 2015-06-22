<?php

namespace Teampass\Api\Service\Encoder;

class DefaultEncoder implements EncoderInterface
{
    /**
     * @var string
     */
    private $key = null;

    /**
     * @param string $decrypted
     * @param string $key
     *
     * @return bool|string
     */
    public function encrypt($decrypted, $key = null)
    {
        return base64_encode($decrypted);
    }

    /**
     * @param string $encrypted
     * @param string $key
     *
     * @return bool|string
     */
    public function decrypt($encrypted, $key = null)
    {
        return base64_decode($encrypted);
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = (string) $key;
    }
}
