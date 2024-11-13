<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('runBackgroundJob')) {
    // Static queue for prioritized job execution
    function runBackgroundJob($class, $method, $parameters = [], $retries = 3, $delay = 1, $priority = 1)
    {
        static $jobQueue = []; // Static queue to hold jobs with priority

        // Add job to queue with priority
        $jobQueue[] = [
            'class' => $class,
            'method' => $method,
            'parameters' => $parameters,
            'retries' => $retries,
            'delay' => $delay,
            'priority' => $priority,
        ];

        // Sort the queue by priority (lower number is higher priority)
        usort($jobQueue, fn($a, $b) => $a['priority'] <=> $b['priority']);

        // Execute each job in order of priority
        foreach ($jobQueue as $job) {
            executeJobWithRetry($job);
        }
    }

    function executeJobWithRetry($job)
    {
        Log::info("Starting runBackgroundJob with retry", $job);

        $serializedParams = escapeshellarg(json_encode($job['parameters']));
        $command = sprintf(
            'php %s artisan job:run %s %s %s > /dev/null 2>&1 &',
            base_path(),
            escapeshellarg($job['class']),
            escapeshellarg($job['method']),
            $serializedParams
        );

        $attempt = 0;
        while ($attempt < $job['retries']) {
            try {
                Log::info("Executing command for background job", ['command' => $command]);

                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    pclose(popen("start /B {$command}", "r"));
                } else {
                    exec($command);
                }

                Log::info("Command executed successfully on attempt #$attempt", [
                    'class' => $job['class'],
                    'method' => $job['method'],
                    'parameters' => $job['parameters'],
                ]);
                break; // Exit loop on success

            } catch (Exception $e) {
                $attempt++;
                if ($attempt >= $job['retries']) {
                    Log::channel('background_jobs_errors')->error("Job permanently failed", [
                        'class' => $job['class'],
                        'method' => $job['method'],
                        'parameters' => $job['parameters'],
                        'status' => 'permanent failure',
                        'attempts' => $attempt,
                        'error' => $e->getMessage(),
                    ]);
                    throw $e;
                }
                sleep($job['delay']); // Delay before next retry
            }
        }
    }
}
