<?php

namespace hiapi\Core\Utils;

class CIDR
{
    public static function match($ip, $range): bool
    {
        if (strpos($range, '/') === false) {
            $range = "$range/32";
        }
        list ($subnet, $bits) = explode('/', $range);

        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;
        return ($ip & $mask) == $subnet;
    }

    public static function matchBulk($ip, $ranges): bool
    {
        $match = false;
        foreach ($ranges as $range => $value) {
            $match = $match ? : (self::match($ip, $range) ? $value : false);
            if ($match) break;
        }
        return $match;
    }
}
