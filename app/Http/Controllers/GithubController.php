<?php

namespace App\Http\Controllers;

use App\Jobs\FetchGithubRepositories;
use App\Jobs\FetchGithubBranches;
use App\Jobs\FetchGithubCommits;
use App\Models\BackgroundJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GithubController extends Controller
{
    public function sync(Request $request)
    {
        $username = $request->username ?: config('services.github.username');
        $githubToken = $request->personalToken ?: config('services.github.token');

        // Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the tables
        DB::table('branches')->truncate();
        DB::table('commits')->truncate();
        DB::table('repositories')->truncate();
        DB::table('background_jobs')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Array of job classes with priorities
        $jobs = [
            [
                'class' => FetchGithubRepositories::class,
                'method' => 'handle',
                'parameters' => [$username, $githubToken],
                'priority' => 1,
            ],
            [
                'class' => FetchGithubBranches::class,
                'method' => 'handle',
                'parameters' => [$username, $githubToken],
                'priority' => 2,
            ],
            [
                'class' => FetchGithubCommits::class,
                'method' => 'handle',
                'parameters' => [$username, $githubToken],
                'priority' => 3,
            ],
        ];

        //  Create job records and add them to the jobs array
        foreach ($jobs as &$job) {
            $job['jobRecord'] = BackgroundJob::create([
                'class' => $job['class'],
                'method' => $job['method'],
                'parameters' => json_encode($job['parameters']),
                'status' => 'pending',
                'priority' => $job['priority'],
            ]);
        }

        // Pass all jobs to runBackgroundJob to handle prioritization
        runBackgroundJob($jobs, $request->delay, $request->retries);

        return back()->with('message', 'Background job runner initiated successfully with priorities.');
    }

    public function clean()
    {
        // Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the tables
        DB::table('branches')->truncate();
        DB::table('commits')->truncate();
        DB::table('repositories')->truncate();
        DB::table('background_jobs')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return back()->with('message', 'Data cleared successfully!');
    }
}
