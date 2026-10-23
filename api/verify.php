<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// NOTE: Vercel serverless functions have an ephemeral, per-invocation filesystem.
// /tmp is NOT shared between invocations, so file-based verification storage
// does not persist. To make the admin control panel work, configure external
// persistent storage (Vercel KV, Upstash Redis, or a database) and replace
// the storage layer below. For now, all endpoints return safe JSON responses.

// Handle GET request - check what prompt to show
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['email']) && !isset($_GET['list'])) {
    $identifier = trim($_GET['email']);

    if (empty($identifier)) {
        http_response_code(400);
        echo json_encode(['error' => 'Identifier required']);
        exit;
    }

    // No persistent storage available on Vercel serverless by default.
    echo json_encode(['prompt_type' => null]);
    exit;
}

// Handle POST request - set verification prompt
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

    // No persistent storage available on Vercel serverless by default.
    // The prompt cannot be saved for later retrieval.
    http_response_code(503);
    echo json_encode([
        'success' => false,
        'error' => 'Persistent storage not available on Vercel serverless. Configure Vercel KV or an external database to use the admin control panel.'
    ]);
    exit;
}

// Handle DELETE request - clear verification prompt
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $identifier = isset($_GET['email']) ? trim($_GET['email']) : '';

    if (empty($identifier)) {
        http_response_code(400);
        echo json_encode(['error' => 'Identifier required']);
        exit;
    }

    // No persistent storage available on Vercel serverless by default.
    http_response_code(503);
    echo json_encode([
        'success' => false,
        'error' => 'Persistent storage not available on Vercel serverless. Configure Vercel KV or an external database to use the admin control panel.'
    ]);
    exit;
}

// Redirect to control panel for UI
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['email']) && !isset($_GET['list'])) {
    header('Location: control.html');
    exit;
}

// Handle list all request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['list']) && $_GET['list'] === 'all') {
    // No persistent storage available on Vercel serverless by default.
    echo json_encode([]);
    exit;
}
?>