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
        runBackgroundJob('App\\Jobs\\ApprovedJob1', 'execute', ['success', 'test parameter'], 3);

        return response()->json(['status' => 'Job dispatched successfully']);
    }

    public function runJobDirectly()
    {
        try {
            // Execute job directly with JobRunner
            $result = JobRunner::run('App\\Jobs\\ApprovedJob1', 'execute', ['success', 'test parameter'], 3);

            return response()->json(['status' => 'Job executed successfully', 'result' => $result]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'Job execution failed', 'error' => $e->getMessage()], 500);
        }
    }
}
