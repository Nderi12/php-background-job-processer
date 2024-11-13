<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('runBackgroundJob')) {
    /**
     * Run a background job based on priority.
     *
     * @param array $jobs Array of job data with 'class', 'method', 'parameters', 'priority', and 'jobRecord'
     */
    function runBackgroundJob(array $jobs, $delaySeconds = 5, $retryAttempts = 3)
    {
        // Sort jobs by priority in ascending order (priority 1 is highest)
        usort($jobs, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        // Loop through each job and execute it based on priority
        foreach ($jobs as $jobData) {
            $class = $jobData['class'];
            $method = 'handle';
            $parameters = $jobData['parameters'];
            $jobRecord = $jobData['jobRecord'];

            // Ensure class and method exist
            if (!class_exists($class) || !method_exists($class, $method)) {
                Log::error("Invalid class or method: {$class}::{$method}");
                continue;
            }

            $jobId = uniqid('job_', true);

            // Check if the job record was created successfully
            if (!$jobRecord) {
                Log::error("Failed to create job record in the database.");
                continue;
            }

            // Generate the command only after confirming the job exists
            $command = generateJobCommand($class, $method, $parameters, $jobRecord->id);
            Log::info("Job {$jobId} created and starting: {$class}::{$method}");

            // Run the job asynchronously
            executeJobInBackground($command);
            
            // Optional delay between job executions for better prioritization
            sleep($delaySeconds);
        }
    }
}

/**
 * Generate the command to run a job
 *
 * @param string $class
 * @param string $method
 * @param array $parameters
 * @param int $jobId
 * @return string
 */
function generateJobCommand($class, $method, $parameters, $jobId)
{
    $encodedParams = json_encode(array_values($parameters));
    $scriptPath = escapeshellarg(base_path('scripts/JobRunner.php'));

    if (PHP_OS === 'WINNT') {
        $encodedParams = escapeshellarg($encodedParams);
        $command = "start /B php -f {$scriptPath} {$jobId} {$encodedParams}";
    } else {
        $command = "php -f {$scriptPath} {$jobId} {$encodedParams} > /dev/null 2>&1 &";
    }

    return $command;
}

/**
 * Execute the job command in the background.
 *
 * @param string $command
 * @return int
 */
function executeJobInBackground($command)
{
    if (PHP_OS === 'WINNT') {
        pclose(popen("cmd /c {$command}", 'r'));
    } else {
        shell_exec($command);
    }

    return 0;
}
