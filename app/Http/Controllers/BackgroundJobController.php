<?php

namespace App\Http\Controllers;

use App\Models\BackgroundJob;
use Illuminate\Http\Request;

class BackgroundJobController extends Controller
{
    public function index()
    {
        // Fetch all background jobs ordered by priority
        $jobs = BackgroundJob::orderBy('priority', 'asc')->get();

        return view('welcome', compact('jobs'));
    }
}
