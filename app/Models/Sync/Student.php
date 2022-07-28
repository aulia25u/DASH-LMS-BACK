<?php

namespace App\Models\Sync;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $connection= 'sync';
    protected $table = 'students';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable =   [
                                'date_sync'
                            ];
}
