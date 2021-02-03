<?php
declare(strict_types=1);

namespace hiapi\Core\Utils;

use yii\helpers\IpHelper;

class CIDR
{
    /**
     * @param string $ip
     * @param array<string, boolean> $ranges
     * @return bool
     * @throws \yii\base\NotSupportedException
     */
    public static function matchBulk(string $ip, array $ranges): bool
    {
        foreach ($ranges as $range => $_) {
            if (IpHelper::inRange($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    public static function match(string $ip, string $range): bool
    {
        return IpHelper::inRange($ip, $range);
    }
}
