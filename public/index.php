<?php

declare(strict_types=1);

/**
 * MCP "Streamable HTTP" endpoint for the TYPO3 core knowledge base, built on the
 * official mcp/sdk. Designed for classic PHP shared hosting: no persistent
 * process. Session state is kept in files (var/sessions) so the stateful
 * Streamable HTTP transport works across independent PHP requests.
 *
 * A static bearer token guards every request before the MCP transport runs.
 */

use Mcp\Server\Session\FileSessionStore;
use Mcp\Server\Transport\StreamableHttpTransport;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Typo3CmsMcp\ServerFactory;

require dirname(__DIR__) . '/src/bootstrap.php';

// --- Authentication: static bearer token, enforced before anything else. ---
$configuredToken = mcp_configured_token();
if ($configuredToken === null || $configuredToken === '') {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Server misconfigured: no auth token set (MCP_AUTH_TOKEN or config.local.php).']);
    exit;
}

$presentedToken = mcp_bearer_token();
if ($presentedToken === null || !hash_equals($configuredToken, $presentedToken)) {
    http_response_code(401);
    header('WWW-Authenticate: Bearer');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Unauthorized.']);
    exit;
}

// --- Build a PSR-7 request from the SAPI globals and run the MCP transport. ---
$psr17 = new Psr17Factory();
$request = (new ServerRequestCreator($psr17, $psr17, $psr17, $psr17))->fromGlobals();

$sessionDir = dirname(__DIR__) . '/var/sessions';
if (!is_dir($sessionDir)) {
    mkdir($sessionDir, 0775, true);
}

$server = ServerFactory::create(new FileSessionStore($sessionDir));
$response = $server->run(new StreamableHttpTransport($request, $psr17, $psr17));

// --- Emit the PSR-7 response. ---
http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header($name . ': ' . $value, false);
    }
}
echo $response->getBody();

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
