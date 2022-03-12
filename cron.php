<?php 
$wait = 60;
set_time_limit(0);
ini_set('memory_limit', '1024M');
while(true){
    echo exec("php artisan schedule:run");
    sleep($wait);
}