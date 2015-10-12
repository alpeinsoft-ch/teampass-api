<?php

namespace Teampass\Api\Service\Platform;

use PHP_Crypt\PHP_Crypt;

class Encoder
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
     * @param string $string
     * @param string $key
     *
     * @return bool|string
     */
    public function encrypt($string, $key = null)
    {
        if (empty($key)) {
            $key = $this->defaultSalt;
        }

        if ($key != $this->defaultSalt) {
            if (strlen($key) < 16) {
                for ($x = strlen($key) + 1; $x <= 16; ++$x) {
                    $key .= chr(0);
                }
            } elseif (strlen($key) > 16) {
                $key = substr($key, 16);
            }
        }

        $crypt = new PHP_Crypt($key, PHP_Crypt::CIPHER_AES_128, PHP_Crypt::MODE_CBC);
        $iv = $crypt->createIV();
        $encrypt = $crypt->encrypt($string);

        return [
            'string' => bin2hex($encrypt),
            'iv' => bin2hex($iv),
        ];
    }

    /**
     * Decrypt data for teampass db.
     *
     * @param string      $string
     * @param string|null $iv
     * @param string|null $key
     *
     * @return bool|string
     */
    public function decrypt($string, $iv = null, $key = null)
    {
        if (empty($key)) {
            $key = $this->defaultSalt;
        }

        if ($key != $this->defaultSalt) {
            if (strlen($key) < 16) {
                for ($x = strlen($key) + 1; $x <= 16; ++$x) {
                    $key .= chr(0);
                }
            } elseif (strlen($key) > 16) {
                $key = substr($key, 16);
            }
        }

        $crypt = new PHP_Crypt($key, PHP_Crypt::CIPHER_AES_128, PHP_Crypt::MODE_CBC);
        if (empty($iv)) {
            return '';
        }
        $string = hex2bin(trim($string));
        $iv = hex2bin($iv);
        $crypt->IV($iv);
        $decrypt = $crypt->decrypt($string);

        return str_replace(chr(0), '', $decrypt);
    }
}
