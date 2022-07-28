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
}