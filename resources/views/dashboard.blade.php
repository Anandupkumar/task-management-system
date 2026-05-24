<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex gap-3">
                <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                    {{ __('View Tasks') }}
                </a>
                @can('create', App\Models\Task::class)
                    <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        {{ __('Create Task') }}
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm">Total Tasks</div>
                    <div class="text-3xl font-bold">{{ $total }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-green-500 text-sm">Completed</div>
                    <div class="text-3xl font-bold">{{ $completed }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-yellow-500 text-sm">Pending</div>
                    <div class="text-3xl font-bold">{{ $pending }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-red-500 text-sm">High Priority</div>
                    <div class="text-3xl font-bold">{{ $highPriority }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 flex justify-center">
                <div class="w-full max-w-md aspect-square">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('statusChart');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    data: [
                        {{ $statusDistribution['pending'] }},
                        {{ $statusDistribution['in_progress'] }},
                        {{ $statusDistribution['completed'] }}
                    ],
                    backgroundColor: [
                        '#fcd34d', // yellow
                        '#93c5fd', // blue
                        '#86efac'  // green
                    ],
                    borderWidth: 1
                }]
            }
        });
    </script>
</x-app-layout>
