<?php

namespace Teampass\Api\Service\Encoder;

use phpseclib\Crypt\AES;

class PSEncoder implements EncoderInterface
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
        $key = is_null($key) ? $this->key : $key;
        $aes = new AES();
        $aes->setKey(hash('SHA256', $key, true));

        return base64_encode($aes->encrypt($decrypted));
    }

    /**
     * @param string $encrypted
     * @param string $key
     *
     * @return bool|string
     */
    public function decrypt($encrypted, $key = null)
    {
        $key = is_null($key) ? $this->key : $key;
        $aes = new AES();
        $aes->setKey(hash('SHA256', $key, true));

        return $aes->decrypt(base64_decode($encrypted));
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = (string) $key;
    }
}
