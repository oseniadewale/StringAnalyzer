<?php
require_once 'classes/StringAnalyzer.php';
require_once 'classes/StringRepository.php';
require_once 'classes/ResponseHelper.php';

$repo = new StringRepository();
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$query = $_GET;

// POST /strings (Create & Analyze)
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

//  GET /strings/{value} (Get one)
if (preg_match('#^/strings/(.+)$#', $path, $matches) && $method === 'GET') {
    $value = urldecode($matches[1]);
    $record = $repo->findByValue($value);
    if (!$record) ResponseHelper::json(['error' => 'Not found'], 404);
    ResponseHelper::json($record, 200);
}

//  GET /strings (Get all or filtered)
if ($path === '/strings' && $method === 'GET') {
    $records = $repo->getAll();

    // Filtering logic
    if (!empty($query)) {
        $records = array_filter($records, function ($r) use ($query) {
            $p = $r['properties'];
            if (isset($query['is_palindrome']) && filter_var($query['is_palindrome'], FILTER_VALIDATE_BOOLEAN) !== $p['is_palindrome']) {
                return false;
            }
            if (isset($query['min_length']) && $p['length'] < (int)$query['min_length']) {
                return false;
            }
            if (isset($query['max_length']) && $p['length'] > (int)$query['max_length']) {
                return false;
            }
            if (isset($query['word_count']) && $p['word_count'] != (int)$query['word_count']) {
                return false;
            }
            if (isset($query['contains_character']) && !str_contains(strtolower($r['value']), strtolower($query['contains_character']))) {
                return false;
            }
            return true;
        });
    }

    ResponseHelper::json([
        'data' => array_values($records),
        'count' => count($records),
        'filters_applied' => $query
    ], 200);
}

// DELETE /strings/{value} (Delete one)
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
