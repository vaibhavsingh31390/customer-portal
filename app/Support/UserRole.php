<?php

namespace App\Support;

class UserRole
{
    public static function isSupport(?string $userCode): bool
    {
        return $userCode !== null && $userCode !== '' && preg_match('/^S/', $userCode) === 1;
    }

    public static function isAdmin(?string $userCode): bool
    {
        return $userCode !== null && $userCode !== '' && preg_match('/^A/', $userCode) === 1;
    }

    public static function isStaff(?string $userCode): bool
    {
        return self::isSupport($userCode) || self::isAdmin($userCode);
    }

    public static function isClient(?string $userCode): bool
    {
        return $userCode !== null && $userCode !== '' && ! self::isStaff($userCode);
    }
}
