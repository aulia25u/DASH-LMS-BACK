<?php

namespace App\Models\Schedule;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $table = 'schedule_exam_slots';
    protected $primaryKey = 'id';
    protected $fillable =   [
                                'slotname',
                                'starttime',
                                'endtime',
                                'is_active'
                            ];
}