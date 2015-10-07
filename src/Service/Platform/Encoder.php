<?php

namespace Teampass\Api\Service\Platform;
include("__DIR__.'/../../vendor/phpcrypt/phpCrypt.php");
use PHP_Crypt\PHP_Crypt as PHP_Crypt;

class Encoder
{
    const ITCOUNT = 2072;

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
     * @return bool|array
     */
    public function encrypt($decrypted, $key = null)
    {
        $salt = null === $key ? $this->defaultSalt : $key;
        //manage key origin
        if (empty($key)) $key = $salt;

        if ($key != $salt) {
            // check key (AES-128 requires a 16 bytes length key)
            if (strlen($key) < 16) {
                for ($x = strlen($key) + 1; $x <= 16; $x++) {
                    $key .= chr(0);
                }
            } else if (strlen($key) > 16) {
                $key = substr($key, 16);
            }
        }
 
        // load crypt
        $crypt = new PHP_Crypt($key, PHP_Crypt::CIPHER_AES_128, PHP_Crypt::MODE_CBC);
	$string = trim($decrypted);
	if(empty($string)) return false;
        //generate IV and encrypt
        $iv = $crypt->createIV();
        $encrypted = $crypt->encrypt($string);
        // return
        return array(
            "string" => bin2hex($encrypted),
            "iv" => bin2hex($iv)
       	);
    }

    /**
     * Decrypt data for teampass db.
     *
     * @param string $encrypted
     * @param string $iv
     * @param string $key
     *
     * @return bool|string
     */
    public function decrypt($encrypted, $iv, $key = null)
    {
        $salt = null === $key ? $this->defaultSalt : $key;
        // manage key origin
        if (empty($key)) $key = $salt;
        if ($key != $salt) {
            // check key (AES-128 requires a 16 bytes length key)
            if (strlen($key) < 16) {
                for ($x = strlen($key) + 1; $x <= 16; $x++) {
                    $key .= chr(0);
                }
            } else if (strlen($key) > 16) {
                $key = substr($key, 16);
            }
        }
        // load crypt
        $crypt = new PHP_Crypt($key, PHP_Crypt::CIPHER_AES_128, PHP_Crypt::MODE_CBC);
        if (empty($iv)) return false;
        $string = hex2bin($encrypted);
        $iv = hex2bin($iv);
        // load IV
        $crypt->IV($iv);
        // decrypt
        $decrypt = $crypt->decrypt($string);
        // return
        return str_replace(chr(0), "", $decrypt);
    }
}
