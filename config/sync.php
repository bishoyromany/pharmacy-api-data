<?php 

use Carbon\Carbon;

$today = Carbon::now();

return [
    'sync_timestamp' => $today->subMonths(4)->timestamp,
    'sync_date' => $today->subMonths(4)->toDateString(),
    'max_allowed_data' => 1000 * 20
];