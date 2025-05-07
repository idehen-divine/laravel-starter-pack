<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtpCode extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'email',
        'code',
        'type',
        'expired_at',
        'is_verified',
    ];
}
