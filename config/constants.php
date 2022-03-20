<?php 

use Carbon\Carbon;

$today = Carbon::now();

return [
    'min_data_sync_timestamp' => $today->subYear()->timestamp,
    'min_data_sync_date' => $today->subYear()->toDateString()
];