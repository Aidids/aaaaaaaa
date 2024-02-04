<?php

namespace App\Api\Controller;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Http\Traits\NotificationTrait;
use App\Models\PortalNotification;
use Carbon\Carbon;

class NotificationApi extends Controller
{
    use NotificationTrait;

    public function index(int $userID)
    {
        $response = PortalNotification::with('user')
            ->where('user_id', $userID)
            ->whereYear('created_at', now()->year)
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();

        return NotificationResource::collection($response);
    }
}
