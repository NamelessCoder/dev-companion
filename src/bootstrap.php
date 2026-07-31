<?php

declare(strict_types=1);

/**
 * Locates and loads the Composer autoloader for the stdio entrypoint.
 *
 * The file lives in a different place depending on how this package is used:
 * in a standalone checkout it is ./vendor/autoload.php, while as an installed
 * dependency the package sits in vendor/typo3/cms-mcp/ and the autoloader is
 * three levels up. Composer's bin proxy tells us the exact path via
 * $_composer_autoload_path, so that is preferred when present.
 */
(static function (): void {
    $candidates = [];

    // Set by Composer's bin proxy — authoritative when the entrypoint was
    // invoked through vendor/bin/.
    if (isset($GLOBALS['_composer_autoload_path'])) {
        $candidates[] = $GLOBALS['_composer_autoload_path'];
    }

    $candidates[] = dirname(__DIR__) . '/vendor/autoload.php'; // standalone checkout
    $candidates[] = dirname(__DIR__, 3) . '/autoload.php';     // vendor/typo3/cms-mcp/

    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            require $candidate;

            return;
        }
    }

    fwrite(STDERR, 'typo3-cms-mcp: Composer autoloader not found.'
        . " Run 'composer install' in the package root.\n");
    exit(1);
})();
