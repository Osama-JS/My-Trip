<?php
$lines = file('c:\xampp\htdocs\my-trip\app\Http\Controllers\Api\V1\TripController.php');
foreach ($lines as $k => $v) {
    if (strpos($v, 'function bookingDetails') !== false) {
        echo ($k+1)."\n";
    }
}
