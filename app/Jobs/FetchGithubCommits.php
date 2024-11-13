<?php

namespace App\Jobs;

use App\Models\Branch;
use App\Models\Commit;
use App\Models\Repository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchGithubCommits
{
    protected $token;
    protected $username;

    public function __construct($username, $token = null)
    {
        $this->username = $username;
        $this->token = $token ?: config('services.github.token');
    }

    public function handle()
    {
        $repositories = Repository::get();

        foreach ($repositories as $repository) {
            $branches = $repository->branches;

            foreach ($branches as $branch) {
                $this->fetchCommitsForBranch($repository, $branch);
            }
        }
    }

    /**
     * Fetch commits for a specific repository and branch.
     *
     * @param Repository $repository
     * @param Branch $branch
     */
    protected function fetchCommitsForBranch(Repository $repository, Branch $branch)
    {
        $commitsUrl = "https://api.github.com/repos/{$repository->owner}/{$repository->name}/commits?sha={$branch->name}";
        $response = Http::withToken($this->token)->get($commitsUrl);

        Log::info("Response: " . $response);

        if ($response->successful()) {
            $commits = $response->json();

            foreach ($commits as $commitData) {
                $commit = $commitData['commit'];

                // Extract commit details
                $commitSha = $commitData['sha'];
                $authorName = $commit['author']['name'];
                $authorEmail = $commit['author']['email'];
                $commitDate = $commit['author']['date'];
                $message = $commit['message'];

                // Save the commit to the database
                Commit::updateOrCreate(
                    ['commit_sha' => $commitSha],
                    [
                        'branch_id' => $branch->id,
                        'author_name' => $authorName,
                        'author_email' => $authorEmail,
                        'commit_date' => $commitDate,
                        'message' => $message,
                    ]
                );
            }
        }
    }
}
