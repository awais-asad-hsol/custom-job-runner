<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('runBackgroundJob')) {
    function runBackgroundJob($class, $method, $parameters = [], $retries = 3)
    {
        $serializedParams = escapeshellarg(json_encode($parameters));
        $command = sprintf(
            'php %s artisan job:run %s %s %s > /dev/null 2>&1 &',
            base_path(),
            escapeshellarg($class),
            escapeshellarg($method),
            $serializedParams
        );

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen("start /B {$command}", "r"));
        } else {
            exec($command);
        }
    }
}
