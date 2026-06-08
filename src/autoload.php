<?php

declare(strict_types=1);

/**
 * Minimal PSR-4-ish autoloader for the Typo3CmsMcp namespace (no Composer needed).
 * Shared by every entrypoint (public/index.php HTTP transport, bin/ stdio transport).
 */

spl_autoload_register(static function (string $class): void {
    $prefix = 'Typo3CmsMcp\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});
