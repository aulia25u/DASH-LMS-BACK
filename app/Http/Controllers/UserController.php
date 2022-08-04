<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\User;

class UserController extends Controller
{
	public function getProfile(Request $request)
	{
		$userid = $request->user()->id;

		$data = User::select(
								'name',
								'role',
								'email',
								'scope'
							)
					->where('id', $userid)
					->first();

		return $data;
	}

	public function getProfileMoodle($userid)
	{
		$response = Http::asForm()->post(env('SINAU_DN'), [
            'wstoken' => env('SINAU_TOKEN'),
            'wsfunction' => 'local_sinau_api_get_users_by_field',
            'moodlewsrestformat' => 'json',
            'field' => 'id',
            'values[0]' => $userid,
        ]);

        $decode = json_decode($response, true);

        if ($decode['exception'] == 'dml_missing_record_exception') 
        {
        	$ret = [];

        	return $ret;
        }
        else
        {
			$ret = $decode['data'][0];

			return $ret;
        }
	}
}