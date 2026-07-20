<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Late cutoff
    |--------------------------------------------------------------------------
    | Time-in scans after this time (24-hour "H:i") are marked "late" instead
    | of "present". Adjust in .env with ATTENDANCE_LATE_CUTOFF=07:30
    */
    'late_cutoff' => env('ATTENDANCE_LATE_CUTOFF', '07:30'),

    /*
    |--------------------------------------------------------------------------
    | Absence check time
    |--------------------------------------------------------------------------
    | The scheduled job (attendance:mark-absent) runs at this time each school
    | day and marks anyone with no scan for the day as "absent", then emails
    | their guardian. Format "H:i".
    */
    'absence_check_time' => env('ATTENDANCE_ABSENCE_CHECK_TIME', '17:00'),

    /*
    |--------------------------------------------------------------------------
    | Duplicate scan window (minutes)
    |--------------------------------------------------------------------------
    | If the same student's fingerprint is scanned again within this many
    | minutes of their last scan, it's treated as an accidental double-tap:
    | no new log entry, no duplicate guardian email.
    */
    'scan_gap_minutes' => (int) env('ATTENDANCE_SCAN_GAP_MINUTES', 2),

    /*
    |--------------------------------------------------------------------------
    | Scanner API token
    |--------------------------------------------------------------------------
    | Shared secret the fingerprint scanner's local app/SDK sends as a Bearer
    | token when it calls POST /api/attendance/scan. Set a long random value
    | in .env: SCANNER_API_TOKEN=xxxxx
    */
    'scanner_token' => env('SCANNER_API_TOKEN'),

];
