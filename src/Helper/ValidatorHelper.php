<?php
declare(strict_types=1);

namespace App\Helper;

class ValidatorHelper
{
    public static function checkIsEmpty($field){
        return empty($field);
    }

    public static function checkIsNotNumericAndNotEmpty($field){
        return empty($field) || !is_numeric($field);
    }

    public static function checkIsInteger($field){
        return preg_match('/^\d+$/', $field);
    }
}