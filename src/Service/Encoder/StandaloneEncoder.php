<?php

namespace Teampass\Api\Service\Encoder;

use phpseclib\Crypt\AES;

class StandaloneEncoder implements EncoderInterface
{
    /**
     * @var AES
     */
    private $aes;

    public function __construct($key)
    {
        $this->aes = new AES();
        $this->aes->setKey(hash('SHA256', $key, true));
    }

    /**
     * @param string $decrypted
     *
     * @return bool|string
     */
    public function encrypt($decrypted)
    {
        return base64_encode($this->aes->encrypt($decrypted));
    }

    /**
     * @param string $encrypted
     *
     * @return bool|string
     */
    public function decrypt($encrypted)
    {
        return $this->aes->decrypt(base64_decode($encrypted));
    }
}
