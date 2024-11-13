<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Log;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$jobId = $argv[1];

$rawParameters = $argv[2];
$formattedParameters = preg_replace('/\s*,\s*/', '","', trim($rawParameters, '[] '));
$formattedParameters = '["' . $formattedParameters . '"]';

$parameters = isset($formattedParameters) ? json_decode($formattedParameters, true) : [];

try {
    $job = \App\Models\BackgroundJob::find($jobId);

    if (!$job) {
        Log::error("Job not found with ID {$jobId}");
        exit(1);
    }

    // Set the job status to 'running' when it starts
    $job->status = 'running';
    $job->save();

    $class = $job->class;
    $method = $job->method;

    if (!class_exists($class) || !method_exists($class, $method)) {
        Log::error("Class or method does not exist: {$class}::{$method}");
        exit(1);
    }

    $reflection = new ReflectionClass($class);

    // Ensure parameters are in an array format
    $instance = $reflection->newInstanceArgs(is_array($parameters) ? $parameters : []);

    call_user_func([$instance, $method]);

    // Mark the job as completed
    $job->status = 'completed';
    $job->save();

    Log::channel('background_jobs')->info("Job completed successfully", [
        'job_id' => $jobId,
        'class' => $class,
        'method' => $method,
    ]);

} catch (Exception $e) {
    // Mark the job as failed
    $job = \App\Models\BackgroundJob::find($jobId);
    if ($job) {
        $job->status = 'failed';
        $job->save();
    }

    Log::channel('background_jobs_errors')->error("Job failed", [
        'job_id' => $jobId,
        'class' => $class,
        'method' => $method,
        'error' => $e->getMessage(),
    ]);
    exit(1);
}
