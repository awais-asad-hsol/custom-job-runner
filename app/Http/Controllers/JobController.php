<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function runBackgroundJob;
use App\Services\JobRunner;
class JobController extends Controller
{
    public function runJobUsingHelperFunction()
    {
        // Call the helper function to run `ApprovedJob1` asynchronously
        runBackgroundJob('App\\Jobs\\ApprovedJob1', 'execute', ['success', 'test parameter'], 3, 2, 2);
        runBackgroundJob('App\\Jobs\\ApprovedJob2', 'execute', ['success', 'test parameter'], 3, 2, 1);

        return response()->json(['status' => 'Job with priority executed using global helper function successfully']);
    }

    public function runJobDirectly()
    {
        try {

            // Execute job directly with JobRunner
            // Add jobs with different priorities
            JobRunner::addJob('App\\Jobs\\ApprovedJob1', 'execute', ['param1', 'param2'], priority: 2, retries: 3, delay: 2);
            JobRunner::addJob('App\\Jobs\\ApprovedJob2', 'execute', ['param1', 'param2'], priority: 1, retries: 3, delay: 2);

            // Run all jobs in priority order
            JobRunner::runJobs();

            return response()->json(['status' => 'Job with priority executed using service function successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'Job execution failed', 'error' => $e->getMessage()], 500);
        }
    }
}
