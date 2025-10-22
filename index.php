<?php
require_once 'classes/StringAnalyzer.php';
require_once 'classes/StringRepository.php';
require_once 'classes/ResponseHelper.php';

$repo = new StringRepository();
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// CREATE / POST /strings
if ($path === '/strings' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['value'])) ResponseHelper::json(['error' => 'Missing value field'], 400);
    if (!is_string($input['value'])) ResponseHelper::json(['error' => 'Value must be a string'], 422);

    $value = trim($input['value']);
    $existing = $repo->findByValue($value);
    if ($existing) ResponseHelper::json(['error' => 'String already exists'], 409);

    $props = StringAnalyzer::analyze($value);
    $record = [
        'id' => $props['sha256_hash'],
        'value' => $value,
        'properties' => $props,
        'created_at' => gmdate('c')
    ];
    $repo->add($record);
    ResponseHelper::json($record, 201);
}

// GET /strings/{value}
if (preg_match('#^/strings/(.+)$#', $path, $matches) && $method === 'GET') {
    $value = urldecode($matches[1]);
    $record = $repo->findByValue($value);
    if (!$record) ResponseHelper::json(['error' => 'Not found'], 404);
    ResponseHelper::json($record, 200);
}

// DELETE /strings/{value}
if (preg_match('#^/strings/(.+)$#', $path, $matches) && $method === 'DELETE') {
    $value = urldecode($matches[1]);
    $record = $repo->findByValue($value);
    if (!$record) ResponseHelper::json(['error' => 'Not found'], 404);
    $repo->deleteByValue($value);
    http_response_code(204);
    exit;
}

// Fallback
ResponseHelper::json(['error' => 'Endpoint not found'], 404);
