<?php 
$wait = 60;
set_time_limit(0);
ini_set('memory_limit', '1024M');
try{
    while(true){
        echo exec("php artisan schedule:run");
        sleep($wait);
    }
}catch(\Exception $e){
    echo exec("php -f cron.php");
}
