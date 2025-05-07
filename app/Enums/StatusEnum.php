<?php

namespace App\Enums;

enum StatusEnum
{
    case PLANNED;
    case IN_PROGRESS;
    case SUSPENDED;
    case CANCELLED;
    case COMPLETED;
    case FAILED;
    case PROCESSING;
    case PENDING;
    case APPROVED;
}
