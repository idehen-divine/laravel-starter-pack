<?php

use App\Helpers\Helper;

if (!function_exists('helpers')) {
    function helpers(): Helper
    {
        return Helper::getInstance();
    }
}
