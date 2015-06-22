<?php

namespace Teampass\Api\Service\Encoder;

class TeampassEncoder implements EncoderInterface
{
    /**
     * @var string
     */
    private $defaultSalt;

    /**
     * @param string $defaultSalt
     */
    public function __construct($defaultSalt = null)
    {
        $this->defaultSalt = $defaultSalt;
    }

    /**
     * Encrypt data for teampass db.
     *
     * @param string $decrypted
     * @param string $key
     *
     * @return bool|string
     */
    public function encrypt($decrypted, $key = null)
    {
        $salt = null === $key ? $this->defaultSalt : $key;
        $pbkdf2Salt = $this->generateBits(64);
        $key = substr(hash_pbkdf2('sha256', $salt, $pbkdf2Salt, EncoderInterface::ITCOUNT, 48, true), 32, 16);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, 'ctr'), MCRYPT_RAND);
        if (strlen($ivBase64 = rtrim(base64_encode($iv), '=')) != 43) {
            return false;
        }
        $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $decrypted, 'ctr', $iv);
        $mac = hash_hmac('sha256', $encrypted, $salt);

        return base64_encode($ivBase64.$encrypted.$mac.$pbkdf2Salt);
    }

    /**
     * Decrypt data for teampass db.
     *
     * @param string $encrypted
     * @param string $key
     *
     * @return bool|string
     */
    public function decrypt($encrypted, $key = null)
    {
        $salt = null === $key ? $this->defaultSalt : $key;
        $encrypted = base64_decode($encrypted);
        $pbkdf2Salt = substr($encrypted, -64);
        $encrypted = substr($encrypted, 0, -64);
        $key = substr(hash_pbkdf2('sha256', $salt, $pbkdf2Salt, EncoderInterface::ITCOUNT, 48, true), 32, 16);
        $iv = base64_decode(substr($encrypted, 0, 43).'==');
        $encrypted = substr($encrypted, 43);
        $mac = substr($encrypted, -64);
        $encrypted = substr($encrypted, 0, -64);

        if (hash_hmac('sha256', $encrypted, $salt) != $mac) {
            return false;
        }

        return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $encrypted, 'ctr', $iv), "\0\4");
    }

    private function generateBits($n)
    {
        $str = '';
        $x = $n + 10;
        for ($i = 0; $i < $x; ++$i) {
            $str .= base_convert(mt_rand(1, 36), 10, 36);
        }

        return substr($str, 0, $n);
    }
}
