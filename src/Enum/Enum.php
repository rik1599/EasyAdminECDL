<?php

namespace App\Enum;

use ReflectionClass;

class Enum 
{
    public static function getAll()
    {
        return (new ReflectionClass(get_called_class()))->getConstants();
    }
}