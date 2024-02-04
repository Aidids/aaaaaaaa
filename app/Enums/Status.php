<?php

namespace App\Enums;

enum Status: string {
    case pending = 'pending';
    case processing = 'processing';
    case canceled = 'canceled';
    case approved = 'approved';
    case rejected = 'rejected';
    case completed = 'completed';
    case expired = 'expired';

    // for notification only
    case success = 'success';
    case hr_approved = 'hr_approved';
    case hr_rejected = 'hr rejected';
    case hr_pending = 'hr_pending'; //use for user notification
    case hr_processing = 'hr_processing'; //use for HR during approval process
}

?>
