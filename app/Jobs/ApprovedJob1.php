<?php

namespace App\Jobs;

use Exception;
use Illuminate\Support\Facades\Log;

class ApprovedJob1
{
    public function execute($param1, $param2)
    {
        // Simulate some processing
        Log::info("Processing job with parameters ApprovedJob1 file: $param1, $param2");

        // Simulate an error for testing retries
        if ($param1 === 'fail') {
            throw new Exception("Simulated job failure for testing retries.");
        }

        // Return success message
        return "Job completed successfully with parameters: $param1, $param2";
    }
}
