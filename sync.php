<?php 
set_time_limit(60*60);
echo exec("php artisan schedule:run");