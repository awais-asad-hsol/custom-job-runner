<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JobRunner;
use Exception;

class RunJob extends Command
{
    protected $signature = 'job:run {class} {method} {parameters?}';
    protected $description = 'Run a specific job in the background';

    public function handle()
    {
        $class = $this->argument('class');
        $method = $this->argument('method');
        $parameters = json_decode($this->argument('parameters'), true) ?? [];
        $retries = 3; // You can make this configurable if needed

        try {
            JobRunner::run($class, $method, $parameters, $retries);
            $this->info("Job executed successfully");
        } catch (Exception $e) {
            $this->error("Job execution failed: " . $e->getMessage());
        }
    }
}
