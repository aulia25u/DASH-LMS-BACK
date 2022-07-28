<?php

namespace App\Models\Sync;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $connection= 'sync';
    protected $table = 'courses';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
