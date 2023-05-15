<?php

namespace App\Utils;

class UtilsRegistrationCode {

    public const CODE_LENGTH = 6;

    /**
     * It generates a random string of CODE_LENGTH characters
     * 
     * @return string A string of CODE_LENGTH random characters.
     */
    public static function getRandom(): string {
        $code = "";
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < self::CODE_LENGTH; $i++) {
            $code .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $code;
    }
}
