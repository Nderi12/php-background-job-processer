<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Background Jobs</title>

    <!-- Tailwind CSS -->
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-gray-100 text-gray-800">

    <!-- Display flash message -->
    @if(session('message'))
        <div class="bg-green-100 text-green-800 border border-green-300 p-4 rounded">
            {{ session('message') }}
        </div>
    @endif

    <h1 class="text-center text-2xl font-bold mb-6">Github Repository Sync Background Jobs</h1>

    <!-- Trigger for Modal -->
    <div class="mt-2 mb-2 flex justify-center">
        <button onclick="openModal()" class="bg-blue-500 mx-2 text-white font-bold py-2 px-4 rounded">
            Configure Background Job
        </button>

        <form method="POST" action="{{ route('clear') }}" onsubmit="return confirm('Are you sure you want to clear all data?');">
            @csrf
            <button type="submit" class="bg-red-500 text-white font-bold py-2 px-4 rounded">
                Clear Data
            </button>
        </form>
    </div>

    <!-- Background Jobs Component -->
    <livewire:background-jobs />

    <!-- Modal -->
    <div id="autoCloseModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded shadow-lg max-w-sm w-full">
            <h2 class="text-lg font-semibold mb-4">Delay Job Execution</h2>
            <form method="POST" action="{{ route('github') }}">
                {{ csrf_field() }}

                <div class="mb-4">
                    <label class="block text-gray-700">Username</label>
                    <input type="text" name="username" id="username" class="border rounded w-full py-2 px-3">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">GithubToken</label>
                    <input type="text" name="personalToken" id="personalToken" class="border rounded w-full py-2 px-3">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Delay (seconds)</label>
                    <input type="number" name="delay" id="delay" class="border rounded w-full py-2 px-3">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700">Retries</label>
                    <input type="number" name="retries" id="retries" class="border rounded w-full py-2 px-3">
                </div>
                <div class="flex justify-between w-full">
                    <button type="submit" class="bg-blue-500 text-white font-bold py-2 px-4 rounded">
                        Execute Job
                    </button>
                    <button type="button" onclick="closeModal()" class="bg-gray-500 text-white font-bold py-2 px-4 rounded mt-2">
                        Close
                    </button>
                </div>
            </form>
        </div>
    </div>

    @livewireScripts

    <!-- JavaScript to control modal open/close -->
    <script>
        let modalOpen = false; // Added a local flag to control modal state

        function openModal() {
            if (!modalOpen) {
                document.getElementById('autoCloseModal').classList.remove('hidden');
                modalOpen = true; // Set the flag to true when the modal is open
            }
        }

        function closeModal() {
            document.getElementById('autoCloseModal').classList.add('hidden');
            modalOpen = false; // Reset the flag when the modal is closed
        }
    </script>
</body>
</html>
