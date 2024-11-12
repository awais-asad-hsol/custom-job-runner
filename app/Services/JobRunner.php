<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class JobRunner
{
    
    public static function run($class, $method, $parameters = [], $retries = 3)
    {
        Log::info("Job started", [
            'class' => $class,
            'method' => $method,
            'parameters' => $parameters,
            'status' => 'running',
            'timestamp' => now()->toDateTimeString(),
        ]);
        $approvedClasses = [
            'App\\Jobs\\ApprovedJob1',
            'App\\Jobs\\ApprovedJob2',
            // Add other approved classes here
        ];
    
        if (!in_array($class, $approvedClasses)) {
            Log::warning("Unauthorized job class attempted", [
                'class' => $class,
                'method' => $method,
                'parameters' => $parameters
            ]);
            throw new Exception("Unauthorized job class: {$class}");
        }
        $attempt = 0;
        while ($attempt < $retries) {
            try {
                // Instantiate the class and execute the method with parameters
                $instance = app($class);
                if (!method_exists($instance, $method)) {
                    throw new Exception("Method {$method} does not exist in class {$class}");
                }

                // Execute the method with parameters
                $result = call_user_func_array([$instance, $method], $parameters);

                // After successful execution
                Log::info("Job executed successfully", [
                    'class' => $class,
                    'method' => $method,
                    'parameters' => $parameters,
                    'status' => 'completed',
                    'timestamp' => now()->toDateTimeString(),
                ]);
                return $result;

            } catch (Exception $e) {
                $attempt++;
                if ($attempt >= $retries) {
                    // Log to the separate error channel
                    Log::channel('background_jobs_errors')->error("Job permanently failed", [
                        'class' => $class,
                        'method' => $method,
                        'parameters' => $parameters,
                        'status' => 'permanent failure',
                        'attempts' => $attempt,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
                // Delay before the next retry
                sleep(1);
            }
        }
    }
}
