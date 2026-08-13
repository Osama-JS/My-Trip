<?php
$data = json_decode(file_get_contents('test_extras.json'), true);
$result = $data['ExtraServicesResponse']['ExtraServicesResult'] ?? [];
$baggage = $result['ExtraServicesData']['DynamicBaggage'] ?? [];
print_r($baggage);
