<?php 

use Carbon\Carbon;

$today = Carbon::now();

return [
    'sync_timestamp' => $today->subYear()->timestamp,
    'sync_date' => $today->subYear()->toDateString(),
    'max_allowed_data' => 1000 * 20
];