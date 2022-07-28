<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncOffset extends Model
{
    protected $table = 'sync_offset';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable =   [
                                'table',
                                'offset',
                                'last_limit'
                            ];
}
