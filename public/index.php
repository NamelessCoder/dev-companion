<?php

declare(strict_types=1);

/**
 * Stateless MCP "Streamable HTTP" endpoint for the TYPO3 core knowledge base.
 * Designed for classic PHP shared hosting: each request is handled fresh, no
 * persistent process, no session state.
 */

use Typo3CmsMcp\McpServer;

require dirname(__DIR__) . '/src/autoload.php';

header('Content-Type: application/json; charset=utf-8');

// --- Method handling: only POST carries JSON-RPC; GET gets no SSE stream. ---
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($requestMethod !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['error' => 'Method Not Allowed. Use POST with a JSON-RPC body.']);
    exit;
}

// --- Authentication: static bearer token. ---
$configuredToken = mcp_configured_token();
if ($configuredToken === null || $configuredToken === '') {
    http_response_code(500);
    echo json_encode(['error' => 'Server misconfigured: no auth token set (MCP_AUTH_TOKEN or config.local.php).']);
    exit;
}

$presentedToken = mcp_bearer_token();
if ($presentedToken === null || !hash_equals($configuredToken, $presentedToken)) {
    http_response_code(401);
    header('WWW-Authenticate: Bearer');
    echo json_encode(['error' => 'Unauthorized.']);
    exit;
}

// --- Parse the JSON-RPC body. ---
$raw = file_get_contents('php://input');
$decoded = json_decode((string) $raw, true);
if (!is_array($decoded)) {
    http_response_code(400);
    echo json_encode(['jsonrpc' => '2.0', 'id' => null, 'error' => ['code' => -32700, 'message' => 'Parse error']]);
    exit;
}

$server = new McpServer();

// A JSON-RPC batch is a list; a single message is an associative array.
$isBatch = array_is_list($decoded);
$messages = $isBatch ? $decoded : [$decoded];

$responses = [];
foreach ($messages as $message) {
    if (!is_array($message)) {
        continue;
    }
    $response = $server->handle($message);
    if ($response !== null) {
        $responses[] = $response;
    }
}

// Only notifications/responses in the request → 202 with no body (per spec).
if ($responses === []) {
    http_response_code(202);
    exit;
}

echo json_encode($isBatch ? $responses : $responses[0], JSON_UNESCAPED_SLASHES);

/**
 * The configured secret: environment first, then an optional gitignored
 * config.local.php returning ['auth_token' => '...'].
 */
function mcp_configured_token(): ?string
{
    $fromEnv = getenv('MCP_AUTH_TOKEN');
    if (is_string($fromEnv) && $fromEnv !== '') {
        return $fromEnv;
    }

    $configFile = dirname(__DIR__) . '/config.local.php';
    if (is_file($configFile)) {
        $config = require $configFile;
        if (is_array($config) && isset($config['auth_token']) && is_string($config['auth_token'])) {
            return $config['auth_token'];
        }
    }

    return null;
}

/**
 * Extracts the bearer token from the Authorization header. Shared hosting often
 * strips it, so several sources are checked (the .htaccess restores it too).
 */
function mcp_bearer_token(): ?string
{
    $header = $_SERVER['HTTP_AUTHORIZATION']
        ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
        ?? '';

    if ($header === '' && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        foreach ($headers as $name => $value) {
            if (strcasecmp($name, 'Authorization') === 0) {
                $header = (string) $value;
                break;
            }
        }
    }

    if (preg_match('/^Bearer\s+(.+)$/i', trim($header), $matches) === 1) {
        return trim($matches[1]);
    }

    return null;
}
