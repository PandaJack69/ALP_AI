<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    protected $fillable = ['user_id', 'subject_name', 'day_of_week', 'start_time', 'end_time'];
}
