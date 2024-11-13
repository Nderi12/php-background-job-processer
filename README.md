# Laravel Background Job Runner System

This project is a background job runner system implemented in a Laravel environment. It includes a user-friendly interface for configuring and executing background jobs with various customizable settings, such as delay, retry attempts, and priority handling. Additionally, it logs job processes and errors, accessible via a simple dashboard.

## Table of Contents
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Running Background Jobs](#running-background-jobs)
  - [Setting Retries, Delays, and Priorities](#setting-retries-delays-and-priorities)
- [Dashboard Features](#dashboard-features)
- [Assumptions, Limitations, and Potential Improvements](#assumptions-limitations-and-potential-improvements)
- [Testing and Logs](#testing-and-logs)
- [Advanced Features](#advanced-features)

## Installation

1. Clone the repository and navigate into the project directory:
   ```bash
   git clone <repository-url>
   cd <project-directory>
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install && npm run dev
   ```

3. Set up your `.env` file:
   - Copy the example file: `cp .env.example .env`
   - Update database configurations and any other necessary environment settings.

4. Generate an application key:
   ```bash
   php artisan key:generate
   ```

5. Run migrations to create the necessary database tables:
   ```bash
   php artisan migrate
   ```

6. Set up log files for background jobs and errors:
   ```bash
   touch storage/logs/background_jobs.log
   touch storage/logs/background_jobs_errors.log
   ```

## Configuration

The system allows for configuration of retry attempts, delays, job priorities, and security settings.

### Steps to Configure Retry Attempts, Delays, and Priorities

1. **Retry Attempts**: In the job configuration modal, specify the number of retry attempts for a job. The system will attempt to retry a job if it fails up to the specified number.

2. **Delay**: Specify the delay (in seconds) to set the interval between job dispatch and actual execution.

3. **Priority**: Choose a priority level (high, medium, or low) for each job. Jobs with higher priority will be queued for earlier execution.

4. **Security Settings**: Configure any required security tokens, such as GitHub tokens, in the job configuration modal. These are stored securely in the session and are required only for authenticated jobs.

## Usage

### Running Background Jobs

To run a background job:
1. Access the job configuration modal by clicking the "Configure Background Job" button on the main page.
2. Fill in the required fields:
   - **Username**: GitHub username (Can add a .env variable GITHUB_USERNAME).
   - **Personal Token**: Token for secure job access ((Can add a .env variable GITHUB_TOKEN)).
   - **Delay**: Time in seconds before the job runs.
   - **Retries**: Number of retry attempts.
3. Submit the form to trigger the job.

#### Example of `runBackgroundJob` Function

The `runBackgroundJob` function is called with parameters like jobs, delay, and retries:
```php
runBackgroundJob('jobs', 10, 3);
```

This function can be further customized by setting priority, which automatically queues it according to urgency.

### Clearing Data

Use the "Clear Data" button on the page to remove all database entries, including job statuses and log data.

## Dashboard Features

The background job runner system includes a dashboard with:
- **Job Status Table**: Displays job ID, status, priority, and job class.
- **Logs Display**: Shows entries from `background_jobs.log` and `background_jobs_errors.log`.
  - These logs are automatically updated every few seconds using `wire:poll.3s="refreshJobs"` for real-time feedback.

## Assumptions, Limitations, and Potential Improvements

- **Assumptions**:
  - Jobs run on a Laravel-compatible queue (e.g., Redis, database).
  - User provides valid GitHub usernames and tokens.
- **Limitations**:
  - Jobs may fail silently without adequate logging if storage logs are incorrectly configured.
  - Lack of granular job status details—could benefit from more specific error handling.
- **Potential Improvements**:
  - Add support for scheduled jobs with advanced timing options (e.g., cron-like scheduling).
  - Consider real-time job progress tracking.
  - Implement more detailed logging, categorizing errors by severity.

## Testing and Logs

### Sample Test Cases

Below are example cases for testing job dispatch and completion:

1. **Test Job Dispatch**:
   - Verify that submitting the job form correctly adds a job to the queue with the specified delay, retry attempts, and priority.
2. **Test Retry Mechanism**:
   - Confirm that jobs retry based on configured attempts after failures.
3. **Test Logs**:
   - Check if `background_jobs.log` and `background_jobs_errors.log` are updated accurately based on job outcomes.

### Sample Logs

**background_jobs.log** (for successful jobs):
```
[2024-11-12 10:00:00] INFO: Job dispatched for username: johndoe, priority: high.
```

**background_jobs_errors.log** (for failed jobs):
```
[2024-11-12 10:05:00] ERROR: Failed job for username: johndoe. Attempt: 2/3.
```

## Advanced Features

### Job Dashboard

The job dashboard displays jobs and logs, showing the status of running, completed, or failed jobs. Each job entry indicates:
- **Priority**: Displays as high, medium, or low.
- **Status**: Real-time status, with a spinner icon for "running" jobs.

