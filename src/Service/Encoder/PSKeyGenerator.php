<?php

namespace Teampass\Api\Service\Encoder;

class PSKeyGenerator
{
    const SALT = '_SUPER_MEGA_SALT';

    public function generate()
    {
        return base64_encode($_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW'].self::SALT);
    }
}
