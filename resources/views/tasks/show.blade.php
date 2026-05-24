<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Task Details') }}: {{ $task->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="md:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-2xl font-bold">{{ $task->title }}</h3>
                            <div class="flex space-x-2">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $task->status->badgeClass() }}">
                                    {{ $task->status->label() }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $task->priority->badgeClass() }}">
                                    {{ $task->priority->label() }}
                                </span>
                            </div>
                        </div>

                        <div class="prose max-w-none text-gray-700 mb-6">
                            {{ $task->description ?: 'No description provided.' }}
                        </div>

                        <div class="border-t border-gray-200 pt-4 flex flex-wrap gap-4 text-sm text-gray-500">
                            <div>
                                <span class="font-semibold">Assigned To:</span> {{ $task->assignedUser->name }}
                            </div>
                            <div>
                                <span class="font-semibold">Due Date:</span> {{ $task->due_date ? $task->due_date->format('M d, Y') : 'N/A' }}
                            </div>
                            <div>
                                <span class="font-semibold">Created:</span> {{ $task->created_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                @can('updateStatus', $task)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold mb-4">Update Status</h4>
                        <form action="{{ route('tasks.update', $task) }}" method="POST" class="flex items-center gap-4">
                            @csrf
                            @method('PUT')
                            <!-- Keep other fields intact for the controller by using hidden inputs, but ideally we'd have a separate status update route for the web UI too. For simplicity in the test, we'll just send the required ones back if needed, or create a specific endpoint. 
                            Actually, let's use the API endpoint via JS or just submit to the main update if the policy allows. 
                            Wait, our UpdateTaskRequest requires title. 
                            Let's just use the API endpoint via Fetch to update status here for a better UI experience. -->
                            
                            <select id="statusSelect" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach(App\Enums\TaskStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ $task->status->value === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <button type="button" id="updateStatusBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Status</button>
                            <span id="statusMessage" class="text-sm hidden text-green-600 ml-2">Updated!</span>
                        </form>
                    </div>
                </div>
                @endcan
            </div>

            <div class="md:col-span-1">
                <div class="bg-gray-50 border border-gray-200 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-bold text-gray-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            AI Analysis
                        </h4>
                        
                        @if($task->ai_summary)
                            <div class="mb-4 text-sm text-gray-700">
                                {{ $task->ai_summary }}
                            </div>
                            <div class="text-sm">
                                <span class="font-semibold text-gray-900">Suggested Priority:</span>
                                <span class="ml-1 px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $task->ai_priority->badgeClass() }}">
                                    {{ $task->ai_priority->label() }}
                                </span>
                            </div>
                        @else
                            <div class="animate-pulse flex space-x-4">
                                <div class="flex-1 space-y-4 py-1">
                                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                    <div class="space-y-2">
                                        <div class="h-4 bg-gray-200 rounded"></div>
                                        <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-4">
                                        Pending AI analysis. Summary will appear here once processed.
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    @can('updateStatus', $task)
    <script>
        document.getElementById('updateStatusBtn').addEventListener('click', async () => {
            const btn = document.getElementById('updateStatusBtn');
            const msg = document.getElementById('statusMessage');
            const status = document.getElementById('statusSelect').value;
            
            btn.disabled = true;
            btn.innerText = 'Updating...';
            
            try {
                const response = await fetch(`/api/tasks/{{ $task->id }}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status })
                });
                
                if (response.ok) {
                    msg.classList.remove('hidden');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert('Failed to update status.');
                    btn.disabled = false;
                    btn.innerText = 'Update Status';
                }
            } catch (error) {
                console.error(error);
                btn.disabled = false;
                btn.innerText = 'Update Status';
            }
        });
    </script>
    @endcan
</x-app-layout>
