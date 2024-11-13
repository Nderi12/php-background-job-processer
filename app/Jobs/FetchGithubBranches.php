<?php

namespace App\Jobs;

use App\Models\Branch;
use App\Models\Repository;
use Illuminate\Support\Facades\Http;
use App\Jobs\FetchGithubCommits;
use Illuminate\Support\Facades\Log;

class FetchGithubBranches
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
            $branchesUrl = str_replace('{/branch}', '', $repository->branches_url);

            $response = Http::withToken($this->token)->get($branchesUrl, [
                'per_page' => 30
            ]);
            
            if ($response->successful()) {
                $branches = $response->json();
    
                foreach ($branches as $branchData) {
                    $branch = Branch::updateOrCreate(
                        [
                            'repository_id' => $repository->id,
                            'name' => $branchData['name'],
                        ],
                        [
                            'commit_sha' => $branchData['commit']['sha']
                        ]
                    );
                }
            }
        }
    }
}
