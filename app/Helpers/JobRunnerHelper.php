<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('runBackgroundJob')) {
    function runBackgroundJob($class, $method, $parameters = [], $retries = 3)
    {
        // Log start of helper function
        Log::info("Starting runBackgroundJob", [
            'class' => $class,
            'method' => $method,
            'parameters' => $parameters,
            'retries' => $retries,
        ]);

        $serializedParams = escapeshellarg(json_encode($parameters));
        $command = sprintf(
            'php %s artisan job:run %s %s %s > /dev/null 2>&1 &',
            base_path(),
            escapeshellarg($class),
            escapeshellarg($method),
            $serializedParams
        );

        Log::info("Constructed command for background job", [
            'command' => $command,
            'os' => PHP_OS
        ]);

        try {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                pclose(popen("start /B {$command}", "r"));
                Log::info("Executed command on Windows");
            } else {
                exec($command);
                Log::info("Executed command on Unix-based system");
            }
        } catch (Exception $e) {
            Log::error("Failed to execute background job command", [
                'error' => $e->getMessage(),
                'command' => $command
            ]);
        }
    }
}
