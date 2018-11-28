<?php
declare(strict_types=1);

namespace App\Helper;

class RouteHelper
{
    public static function replaceParamInRoute(array $data, string $route)
    {
        foreach ($data as $pattern => $replace) {
            $route = str_replace($pattern, $replace, $route);
        }
        return $route;
    }
}