<?php

namespace App\Enums;

enum ServerEnum
{
    case LOCAL;
    case STAGING;
    case DEVELOPMENT;
    case PRODUCTION;
    case TESTING;
}
