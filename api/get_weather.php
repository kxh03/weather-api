<?php
header('Content-Type: application/json');

function callAPI($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return false;
    }
    return json_decode($response, true);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $street = urlencode($_POST['street'] ?? '');
    $number = urlencode($_POST['number'] ?? '');
    $city = urlencode($_POST['city'] ?? '');
    $province = urlencode($_POST['province'] ?? '');
    $zip_code = urlencode($_POST['zip_code'] ?? '');

    if (!$street || !$number || !$city || !$province || !$zip_code) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all mandatory fields']);
        exit;
    }

    $addressUrl = "https://melchior.moja.it:8085/map-api/convert-address?street=$street&number=$number&city=$city&province=$province&zip_code=$zip_code";
    $addressData = callAPI($addressUrl);


    $latitude = $addressData['latitude'];
    $longitude = $addressData['longitude'];

    if (!isset($latitude) || !isset($longitude)) {
        echo json_encode(['success' => false, 'message' => 'Coordinates not found']);
        exit;
    }

    $weatherUrl = "https://melchior.moja.it:8085/weather-api/get_weather?lat=$latitude&lon=$longitude";
    $weatherData = callAPI($weatherUrl);

    /*array(3) {
  ["current_temp"]=>
  float(31.77)
  ["feels_like"]=>
  float(33.72)
  ["weather_description"]=>
  string(9) "clear sky"
  array(1) {
  ["error"]=>
  string(19) "Rate limit exceeded"
}

array(1) {
  ["error"]=>
  string(19) "Rate limit exceeded"
}

}*/

    if (!$weatherData || !isset($weatherData['current_temp'])) {
        echo json_encode(['success' => false, 'message' => 'Error fetching data - '.$weatherData['error']]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'current_temp' => $weatherData['current_temp'],
        'feels_like' => $weatherData['feels_like'],
        'weather_description' => $weatherData['weather_description']
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Only POST method allowed']);
exit;
