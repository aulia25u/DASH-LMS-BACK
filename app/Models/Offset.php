<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offset extends Model
{
    protected $table = 'exam_offsets';
    protected $primaryKey = 'id';
    protected $fillable =   [
                                'item',
                                'offset'
                            ];
}
