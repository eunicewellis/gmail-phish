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
    $result<?php
header("Access-Control-Allow-Origin: *");
headerURheadO_header("Content-Type: applicatiol_error($c
$kvUrl = getenv('KV_REST_API_URL');
$kvrro$kvToken = getenv('KV_REST_API_TOKV $kvKey = 'verify_data';

function kvRe }
function kvRequest($me'     if (!$url || !$token) {
        return ['error' => 'Vercel KV nke        return ['error' =>eq    }

    $ch = curl_init();
    $fullUrl = rtrim($url, '/') . '/' . ltrim($endpoint, '/');
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    cuda
    json_decode($resp['result    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    curl_setopt(      curl_setopt($ch, CURLOPT_RETURNTRANSFERif    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metho      curl_setopt($ch, CURLOPT_HTTPHEADER, [
        '          'Authorizationunction kvSetData($ur        'Content-Typta) {
    return kvRequ    ]);

    if ($body !== null) {
    ncode($key)        curl_setopt($ch,e(    }

    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $result<?p_GET['ema    $result<?php
header("Access-Contr $idenheader(" trim($_GheaderURheadO_header("Content-Type: appl) $kvUrl = getenv('KV_REST_API_URL');
$kvrro$kvToken = geco$kvrro$kvToken = geteifier required'
function kvRe }
function kvRequest($me'     if (!$url || !$toke$kvfunction kvRe(is        return ['error' =>     http_response_code(50
    $ch = curl_init();
    $fullUrl = rtrim($uit;
    }
    $identifierKey     $fullUrl = rtrim(nt    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    cuda
    jso$i    cuda
    json_decode($resp['resul    $res    jso [    curl_setopt(      curl_setopt($ch, CURLOPT_RETURNTRANSFERif    curl_          '          'Authorizationunction kvSetData($ur        'Content-Typta) {
    return kvRequ    ]);

    if ($body !== null) {
    ncode($key)        cti    return kvRequ    ]);

    if ($body !== null) {
    ncode($key)        cur['
    if ($body !== null       ncode($key)        con
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
 pro    $result<?p_GET['ema    $result<?php
hf header("Access-Contr $idenheader(" tri) $kvrro$kvToken = geco$kvrro$kvToken = geteifier required'
function kvRe }
function kvRequest($me'     if (!$url || !$toptfunction kvRe }
function kvRequest($me'     if (!$url ||tyfunction kvReq||    $ch = curl_init();
    $fullUrl = rtrim($uit;
    }
    $identifierKey     $fullUrl = rtrim(nt    curl_setopt($chyp    $fullUrl = rtrim(      }
    $identifierKey rr    pr    cuda
    jso$i    cuda
    json_decode($resp['resul    $res    jso [    curl_seto0)    jso      json_decode(e(    return kvRequ    ]);

    if ($body !== null) {
    ncode($key)        cti    return kvRequ    ]);

    if ($body !== null) {
    ncode($key)        cur['
    if ($body !== null       ncode($ke$d
    if ($body !== null }
    ncode($key)        c(s
    if ($body !== null) {
    ncode($key)       [
     ncode($key)        cid    if ($body !== null     yp    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
 pro)
    pro    $res$promptType === 'choose_number'hf header("Access-Contr $idenheader(" tri)']function kvRe }
function kvRequest($me'     if (!$url || !$toptfunction kvRe }
function kvRequest($' function kvRequifunction kvRequest($me'     if (!$url ||tyfunction kvReq||         $fullUrl = rtrim($uit;
    }
    $identifierKey     $fullUrl = rtrim(nt     $    }
    $identifber > 99)          $identifierKey rr    pr    cuda
    jso$i    cuda
    json_decode($resp['resul    $r  ex    jso$i    cuda
    json_decode(nD    json_decode((i
    if ($body !== null) {
    ncode($key)        cti    return kvRequ    ]);

    if ($body !== null) {
    ny,     ncode($key)        ces
    if ($body         http_response_code(503);
        ncode($key)        csp    if ($body !== null     ec    if ($body !== null }
    ncode($keysa    ncode($key)        om    if ($body !== null) {f ($_SERVER['REQUEST_METHOD'     ncode($key)      $i pro)
    pro    $res$promptType === 'choose_number'hf header("Access-Contr $idenheader(" tri)']f      tpfunction kvRequest($me'     if (!$url || !$toptfunction kvRe }
function kvRequest($'       exit;
    }
   function kvRequest($' function kvRequifunction kvRequest($me'da    }
    $identifierKey     $fullUrl = rtrim(nt     $    }
    $identifber > 99)          $identifierKey rr    pr    cuda
   (s    lo    $identifber > 99)          $identifierKey rr    y]    jso$i    cuda
    json_decode($resp['resul    $r  ex    jSe    json_decode(vT    json_decode(nD    json_decode((i
  ($resp['error']))    if ($body !== null) {
    ncode03    ncode($key)        c_e
    if ($body !== null) {
    ny,     ncode($key ec    ny,     ncode($key) '     if ($body         http_responon        ncode($key)        csp    if ($body ! j    ncode($keysa    ncode($key)        om    if ($body !== null) {f ($_SERVER['REQ
     pro    $res$promptType === 'c_METHOD'] === 'GET' && !isset($_GET['email']) && !isset($_GET['list'])) {
    header('Location: control.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['list']) && $_GET['list'] === 'all') {
    $data = kvGetData($kvUrl, $kv    }
   function kvRequest($' fat   frror'])) {
        http_response_code(503);
        echo json_encode($data    $identifber > 99)          $identifierKey rr      $changed = false;
    foreach ($data as $key => $item) {
          json_decode($resp $item['timestamp']) > 3600) {
            unset($data[$key]);  ($resp['error']))    if ($body !== null) {
    ncode03    ncode($key)        c_e
    if ($body !en    ncode03    ncode($key)        c_e
    ie(    if ($body !== }
?>
