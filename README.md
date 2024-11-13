# Laravel Background Job Runner

## Overview

This system is designed to execute jobs in the background in a Laravel environment. It provides flexibility to configure retry attempts, delays between retries, job priorities, and logging. The job runner supports both synchronous and asynchronous execution through a helper function and service.

## Features
- **Job Retry Logic**: Configure retry attempts and delays between retries.
- **Job Prioritization**: Define job priorities to ensure higher-priority jobs are executed first.
- **Security**: Ensure only approved job classes and methods are executed.
- **Logging**: Detailed logs for job execution, including retries, successes, and errors.

## Setup Instructions

### 1 Install and Configure the System

1. **Add the Helper Function to `composer.json`**:
   Ensure the helper file is autoloaded by adding it to the `autoload` section in `composer.json`:

   ```json
   "autoload": {
       "files": [
           "app/Helpers/JobRunnerHelper.php"
       ]
   }
2. **Run Composer Autoload: Regenerate the autoload files**:

composer dump-autoload

3. **Create the Required Log Channel: Configure a log channel in config/logging.php for error handling**:

'background_jobs_errors' => [
    'driver' => 'single',
    'path' => storage_path('logs/background_jobs_errors.log'),
    'level' => 'error',
],

### 2 Using the Background Job Runner

You can use the background job runner in two ways: with a helper function or directly via the service.

1. **Using the Global Helper Function**
To run a job asynchronously using the runBackgroundJob helper function, simply call the function from a route or controller:
Route::get('/run-job-helper-function', [JobController::class, 'runJobUsingHelperFunction']);

2. **Using the Service Directly**
To execute a job synchronously, you can directly call the JobRunner service in your controller:
Route::get('/run-job-service', [JobController::class, 'runJobDirectly']);

## Job Priorities
To configure job priorities, you can use the following structure:

Add jobs to a queue with different priorities:
JobRunner::addJob('App\\Jobs\\ApprovedJob1', 'execute', ['param1', 'param2'], 1, 3, 2); // Priority 1 (high)
JobRunner::addJob('App\\Jobs\\ApprovedJob2', 'execute', ['param1', 'param2'], 2, 3, 2); // Priority 2 (low)

## Security
For security, the JobRunner service checks if the class is in the approved list. If an unauthorized class is attempted, it throws an exception:

$approvedClasses = [
    'App\\Jobs\\ApprovedJob1',
    'App\\Jobs\\ApprovedJob2',
];

## Sample Log Files
Log entries are recorded for each job execution. The following log files are generated:

laravel log: Logs general job activity such as job starts, completions, and errors.
background_jobs_errors.log: Logs errors, including retries, permanent failures, and exceptions during job execution.
Example log entries:

[2024-11-15 14:35:00] local.INFO: Job started  
  class: App\\Jobs\\ApprovedJob1  
  method: execute  
  parameters: ['success', 'test parameter']  
  status: running  

[2024-11-15 14:35:02] local.INFO: Job executed successfully  
  class: App\\Jobs\\ApprovedJob1  
  method: execute  
  parameters: ['success', 'test parameter']  
  status: completed  

[2024-11-15 14:35:05] local.ERROR: Job permanently failed  
  class: App\\Jobs\\ApprovedJob2  
  method: execute  
  parameters: ['fail', 'test parameter']  
  status: permanent failure  
  attempts: 3  
  error: Simulated job failure for testing retries


## Testing
Test the job runner by using the controller methods:

Visit /run-job-helper-function to test the asynchronous job dispatch using the helper function.
Visit /run-job-service to test the synchronous job execution directly via the JobRunner service.

### Conclusion
This setup enables efficient background job handling with configurable retry attempts, delays, and priorities. You can execute jobs in the background or synchronously while tracking the process with detailed logging.
