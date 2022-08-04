<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'exam_statistic_reports';
    protected $primaryKey = 'id';
    protected $fillable =   [
                                'quiz_id',
                                'group_id',
                                'interval',
                                'detail',
                            ];
}
