<?php

namespace App\Models\SyncMasterData;

use Illuminate\Database\Eloquent\Model;

class SyncUser extends Model
{
    protected $table = 'sync_user';
    protected $primaryKey = 'userid';
    public $timestamps = false;

    protected $fillable =   [
                                'userid',
                                'name',
                                'nim_nip',
                                'username',
                                'email',
                                'jenis',
                                'sync_status',
                                'photo'
                            ];

    public static function ViewSyncUser($perpage=10, $jenis=null, $keyword=null)
    {
        $query = SyncUser::select(
                                    'userid',
                                    'name',
                                    'nim_nip',
                                    'username',
                                    'email',
                                    'jenis',
                                    'sync_status',
                                    'photo'
                                );
        
        $query->where('sync_status', 'SYNCED TO LMS');

        //Check jenis Filter
        if (!is_null($jenis) && $jenis != 0) 
        {
            $query->where('jenis', $jenis);
        }

        //Check Keywords Filter
        if (!is_null($keyword)) 
        {   
            $query->where('name', 'ilike', '%'.$keyword.'%');
        }

        $data = $query->orderBy('userid')->paginate($perpage);

        return $data;
    }
}
