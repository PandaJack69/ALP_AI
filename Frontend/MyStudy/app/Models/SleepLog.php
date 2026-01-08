<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SleepLog extends Model
{
    protected $fillable = ['user_id', 'sleep_at', 'wake_at', 'quality_rating'];
}
