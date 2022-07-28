<?php

namespace App\Models\Sync;

use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    protected $connection= 'sync';
    protected $table = 'lecturers';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable =   [
                                'date_sync'
                            ];
}
