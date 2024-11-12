<?php

use App\Services\JobRunner;

if (!function_exists('runBackgroundJob')) {
    function runBackgroundJob($class, $method, $parameters = [], $retries = 3)
    {
        // Serialize parameters to pass via command line
        $serializedParams = escapeshellarg(json_encode($parameters));

        // Command to execute the Artisan command in the background
        $command = sprintf(
            'php %s artisan job:run %s %s %s > /dev/null 2>&1 &',
            base_path(),
            escapeshellarg($class),
            escapeshellarg($method),
            $serializedParams
        );

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows background execution
            pclose(popen("start /B {$command}", "r"));
        } else {
            // Unix-based background execution
            exec($command);
        }
    }
}
