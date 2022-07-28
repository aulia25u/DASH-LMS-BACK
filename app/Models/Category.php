<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'exam_categories';
    protected $primaryKey = 'id';
    protected $fillable =   [
                                'category_id',
                                'category_name',
                                'category_desc',
                                'category_parent',
                            ];
}
