<?php

namespace App\Utils;

class UtilsRoles {
    public const DEVELOPER = 0;
    public const EDUCATOR = 1;
    public const USER = 2;
    public const ADMIN = 3;

    public static function isValidRole(int $role): bool {
        return $role == self::DEVELOPER || $role == self::EDUCATOR || $role == self::USER || $role == self::ADMIN;
    }
}
