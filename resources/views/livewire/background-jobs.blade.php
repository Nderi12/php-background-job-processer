<div class="container mx-auto mt-10" wire:poll.3s="refreshJobs">

    <!-- Cards for Repos, Branches, and Commits Counts -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <h3 class="text-gray-700 text-xl font-semibold">Repositories</h3>
            <p class="text-2xl font-bold">{{ $repoCount }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <h3 class="text-gray-700 text-xl font-semibold">Branches</h3>
            <p class="text-2xl font-bold">{{ $branchCount }}</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow text-center">
            <h3 class="text-gray-700 text-xl font-semibold">Commits</h3>
            <p class="text-2xl font-bold">{{ $commitCount }}</p>
        </div>
    </div>

    @if($jobs->isEmpty())
        <div class="text-center text-gray-500">No background jobs available at the moment.</div>
    @else
        <table class="w-full bg-white shadow-md rounded border border-gray-200">
            <thead class="bg-gray-100 border-b border-gray-200">
                <tr>
                    <th class="p-4 text-left">Job ID</th>
                    <th class="p-4 text-left">Class</th>
                    <th class="p-4 text-left">Status</th>
                    <th class="p-4 text-left">Priority</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobs as $job)
                    <tr class="border-b border-gray-200">
                        <td class="p-4">{{ $job->id }}</td>
                        <td class="p-4">{{ $job->class }}</td>
                        <td class="p-4">
                            <span class="inline-flex items-center rounded-md 
                                {{ $job->status == 'completed' ? 'bg-green-100 text-green-800 ring-green-600/20' : '' }}
                                {{ $job->status == 'pending' ? 'bg-yellow-100 text-yellow-800 ring-yellow-600/20' : '' }}
                                {{ $job->status == 'running' ? 'bg-blue-100 text-blue-800 ring-blue-600/20' : '' }}
                                {{ $job->status == 'failed' ? 'bg-red-100 text-red-800 ring-red-600/20' : '' }}
                                px-2 py-1 text-xs font-medium ring-1 ring-inset">
                                
                                <!-- Display spinner if the status is 'running' -->
                                {!! $job->status == 'running' ? 
                                    '<svg class="animate-spin h-4 w-4 mr-2 text-blue-800" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <circle cx="12" cy="12" r="10" stroke-width="4" stroke="currentColor" fill="none"></circle>
                                        <path d="M4 12a8 8 0 0 1 16 0" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"></path>
                                    </svg>'
                                : '' !!}
                        
                                {{ $job->status }}
                            </span>
                        </td>                                                
                        <td class="p-4">
                            <span class="inline-flex items-center rounded-md 
                                {{ $job->priority == '3' ? 'bg-blue-100 text-green-800 ring-green-600/20' : '' }}
                                {{ $job->priority == '2' ? 'bg-yellow-100 text-yellow-800 ring-yellow-600/20' : '' }}
                                {{ $job->priority == '1' ? 'bg-red-100 text-red-800 ring-red-600/20' : '' }}
                                px-2 py-1 text-xs font-medium ring-1 ring-inset">
                                {{ $job->priority === 1 ? 'high' : ($job->priority === 2 ? 'medium' : 'low') }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="container mx-auto mt-6">
        <!-- Table for Background Jobs Log -->
        <h2 class="text-lg font-semibold mb-4">Background Jobs Log</h2>
        <div class="bg-white shadow-md rounded border border-gray-200 mb-6 overflow-y-auto max-h-64">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="p-4 text-left">Timestamp</th>
                        <th class="p-4 text-left">Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(file(storage_path('logs/background_jobs.log')) as $line)
                        @php
                            $parts = explode(' ', trim($line), 2); // Split only at the first space
                            $timestamp = $parts[0] ?? null;
                            $message = $parts[1] ?? $line;
    
                            try {
                                $formattedTimestamp = $timestamp ? \Carbon\Carbon::parse($timestamp)->toDateTimeString() : 'Invalid date';
                            } catch (Exception $e) {
                                $formattedTimestamp = 'Invalid date';
                            }
                        @endphp
                        <tr class="border-b border-gray-200">
                            <td class="p-4">{{ $formattedTimestamp }}</td>
                            <td class="p-4">{{ $message }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    
        <!-- Table for Background Jobs Errors Log -->
        <h2 class="text-lg font-semibold mb-4">Background Jobs Errors Log</h2>
        <div class="bg-white shadow-md rounded border border-gray-200 overflow-y-auto max-h-64">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="p-4 text-left">Timestamp</th>
                        <th class="p-4 text-left">Error Message</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(file(storage_path('logs/background_jobs_errors.log')) as $line)
                        @php
                            preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $timestamp, $matches);
                            $formattedTimestamp = isset($matches[1]) 
                                ? \Carbon\Carbon::parse($matches[1])->toDateTimeString()
                                : 'Invalid date';
                        @endphp
                        <tr class="border-b border-gray-200">
                            <td class="p-4">{{ $formattedTimestamp }}</td>
                            <td class="p-4">{{ $message }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>    
</div>
