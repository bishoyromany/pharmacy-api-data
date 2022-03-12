<?php 
$wait = 60;

while(true){
    echo exec("php artisan schedule:run");
    sleep($wait);
}