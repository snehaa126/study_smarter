<?php
session_start();

// Check if an image has been uploaded
if (!isset($_SESSION['uploaded_image']) || empty($_SESSION['uploaded_image'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No image uploaded'
    ]);
    exit;
}

$image_path = $_SESSION['uploaded_image'];

// Ensure the file exists
if (!file_exists($image_path)) {
    echo json_encode([
        'success' => false,
        'message' => 'Image file not found'
    ]);
    exit;
}

// Your OCR.space API key
$apiKey = 'K88017595388957'; // Replace with your actual API key

// Set up the API request
$url = 'https://api.ocr.space/parse/image';
$image = new CURLFile($image_path);

$data = [
    'apikey' => $apiKey,
    'language' => 'eng',
    'isOverlayRequired' => 'false',
    'file' => $image,
];

// Make the API request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo json_encode([
        'success' => false,
        'message' => 'API request failed: ' . $error
    ]);
    exit;
}

// Parse the JSON response
$result = json_decode($response, true);

if (isset($result['ParsedResults']) && !empty($result['ParsedResults'])) {
    $extractedText = $result['ParsedResults'][0]['ParsedText'];
    
    echo json_encode([
        'success' => true,
        'text' => $extractedText
    ]);
} else {
    $errorMessage = isset($result['ErrorMessage']) ? $result['ErrorMessage'] : 'Unknown error occurred';
    
    echo json_encode([
        'success' => false,
        'message' => 'Text extraction failed: ' . $errorMessage
    ]);
}
?>
