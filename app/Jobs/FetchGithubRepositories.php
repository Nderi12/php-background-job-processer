<?php

namespace App\Jobs;

use App\Models\Repository;
use Illuminate\Support\Facades\Http;
use App\Jobs\FetchGithubBranches;

class FetchGithubRepositories
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
        $url = "https://api.github.com/users/{$this->username}/repos";
        
        $response = Http::withToken($this->token)->get($url);

        if ($response->successful()) {
            $repositories = $response->json();

            foreach ($repositories as $repo) {
                $repository = Repository::updateOrCreate(
                    ['github_id' => $repo['id']],
                    [
                        'name' => $repo['name'],
                        'description' => $repo['description'],
                        'owner' => $this->username,
                        'html_url' => $repo['html_url'],
                        'branches_url' => $repo['branches_url'],
                    ]
                );
            }
        }
    }
}
