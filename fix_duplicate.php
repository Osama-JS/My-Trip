<?php
$content = file_get_contents('app/Http/Controllers/Api/V1/TripController.php');
$parts = explode('#[OA\Get(', $content);
$newContent = '';
$found = 0;
foreach ($parts as $i => $part) {
    if ($i == 0) {
        $newContent .= $part;
        continue;
    }
    if (strpos($part, 'public function bookingDetails') !== false) {
        $found++;
        if ($found == 2) {
            continue; // Skip the second one
        }
    }
    $newContent .= '#[OA\Get(' . $part;
}
file_put_contents('app/Http/Controllers/Api/V1/TripController.php', $newContent);
echo "Done";
