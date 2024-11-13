<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\BackgroundJob;
use App\Models\Branch;
use App\Models\Commit;
use App\Models\Repository;

class BackgroundJobs extends Component
{
    public $jobs;
    public $repoCount;
    public $branchCount;
    public $commitCount;

    public function mount()
    {
        $this->fetchJobs();
    }

    public function fetchJobs()
    {
        $this->jobs = BackgroundJob::orderBy('priority')->get();
    }

    public function render()
    {
        $this->fetchJobs();
        $this->repoCount = Repository::count();
        $this->branchCount = Branch::count();
        $this->commitCount = Commit::count();

        return view('livewire.background-jobs');
    }

    public function refreshJobs()
    {
        $this->fetchJobs();
    }

    public function startPolling()
    {
        $this->emit('refreshJobs');
    }
}
