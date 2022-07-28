<?php

namespace App\Models\SyncMasterData;

use Illuminate\Database\Eloquent\Model;

class SyncCategory extends Model
{
    protected $table = 'sync_category as A';
    protected $primaryKey = 'category_id';
    public $timestamps = false;

    protected $fillable =   [
                                'category_id',
                                'category_name',
                                'shortname',
                                'category_type',
                                'initial_studyprogram',
                                'cateogry_parent_id',
                                'group_leader',
                                'sync_date',
                                'sync_status',
                                'flag_status',
                                'table_owner',
                                'table_id',
                                'updated_date',
                                'updated_id',
                                'is_manual_insert',
                                'category_desc',
                                'category_status'
                            ];

    public static function ViewCategory($type=null)
    {
        if (is_null($type)) 
        {
            $data = SyncCategory::select(   
                                            'A.category_id',
                                            'A.category_name',
                                            'A.shortname',
                                            'A.category_type',
                                            'A.initial_studyprogram',
                                            'A.group_leader',
                                            'B.category_name as cateogry_parent'
                                        )
                                ->leftJoin('sync_category as B', 'A.cateogry_parent_id', '=', 'B.category_id')
                                ->orderBy('A.category_id')
                                ->get();
        }
        else
        {
            switch ($type) 
            {
                case '1':
                    $type = 'FACULTY';
                break;
                
                case '2':
                    $type = 'STUDYPROGRAM';
                break;

                case '3':
                    $type = 'SCHOOLYEAR';
                break;
            }

            $data = SyncCategory::select(
                                            'A.category_id',
                                            'A.category_name',
                                            'A.shortname',
                                            'A.category_type',
                                            'A.initial_studyprogram',
                                            'A.group_leader',
                                            'B.category_name as cateogry_parent'
                                        )
                                ->leftJoin('sync_category as B', 'A.cateogry_parent_id', '=', 'B.category_id')
                                ->where('A.category_type', $type)
                                ->orderBy('A.category_id')
                                ->get();
        }

        return $data;
    }

    public static function ViewFilterFaculty()
    {
        $data = SyncCategory::select('category_id', 'category_name')->where('category_type', 'FACULTY')->distinct()->get();

        return $data;
    }

    public static function ViewFilterStudyProgram()
    {
        $data = SyncCategory::select('category_id', 'category_name')->where('category_type', 'STUDYPROGRAM')->distinct()->get();

        return $data;
    }

    public static function ViewFilterSchoolYear()
    {
        $data = SyncCategory::select('category_id', 'category_name')->where('category_type', 'SCHOOLYEAR')->distinct()->get();

        return $data;
    }

    public static function FilterFaculty()
    {
        $data = SyncCategory::select(
                                        'category_id',
                                        'category_name'
                                    )
                            ->where('category_type', 'FACULTY')
                            ->orderBy('category_id')
                            ->get();

        return $data;
    }

    public static function FilterStudyProgram($parent=null)
    {
        $query = SyncCategory::select('category_id', 'category_name');

        $query->where('category_type', 'STUDYPROGRAM');

        if (!is_null($parent)) 
        {
            $query->where('cateogry_parent_id', $parent);
        }

        $data = $query->orderBy('category_id')->get();

        return $data;
    }

    public static function FilterSchoolYear($parent=null)
    {
        $query = SyncCategory::select('category_id', 'category_name');

        $query->where('category_type', 'SCHOOLYEAR');

        if (!is_null($parent)) 
        {
            $query->where('cateogry_parent_id', $parent);
        }

        $data = $query->orderBy('category_id')->get();

        return $data;
    }
}
