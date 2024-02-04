<?php

namespace App\Enums;

enum LeaveRequestDateType: string {
    case fullDay = 'full day';
    case morning = 'morning';
    case evening = 'evening';
}
?>
