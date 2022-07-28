<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Schedule\Slot;

class SlotController extends Controller
{
    //Create or Update Slot
    public function CreateSlot(Request $request)
    {
        $save = Slot::updateOrCreate(
                                        [
                                            'id' => isset($request->id) ? $request->id : '0',
                                        ],
                                        [
                                            'slotname' => $request->slotname,
                                            'starttime' => $request->starttime,
                                            'endtime' => $request->endtime,
                                            'is_active' => 1,
                                        ]
                                    );

        if ($save) 
        {
            return response()->json(['status' => 'Success', 'message' => 'Success update schedule slot'], 200);
        }
        else
        {
            return response()->json(['status' => 'Failed', 'message' => 'Failed update schedule slot'], 500);
        }
    }

    //View Slot
    public function ViewSLot()
    {
        $data = Slot::select('id', 'slotname', 'starttime', 'endtime')->where('is_active', 1)->orderBy('id')->get();

        return $data;
    }
}
