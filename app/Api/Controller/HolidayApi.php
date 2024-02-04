<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Resources\HolidayResource;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HolidayApi extends Controller
{
    public function index(Request $request)
    {
        $date = Carbon::now();

        array_key_exists('date', $request->toArray()) &&
            $date = Carbon::createFromFormat('Y-m-d', $request->get('date'));

        $holidays = Holiday::with('user')
            ->where('date', '>=', $date->copy()->firstOfYear()->toDateString() )
            ->where('date', '<=', $date->copy()->addYear()->endOfYear()->toDateString() )
            ->get();

        return HolidayResource::collection($holidays);
    }
}
