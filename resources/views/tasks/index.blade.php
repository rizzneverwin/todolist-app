@extends('layouts.app')

@section('title', 'All Tasks')

@section('content')

<h1 class="text-2xl font-bold mb-6">All Tasks</h1>

<div class="space-y-6">

    @forelse($tasks as $task)
        <div class="glass p-6 rounded-2xl border border-white/10 shadow-lg">

            {{-- HEADER --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                
                {{-- LEFT: TITLE + DESCRIPTION --}}
                <div class="w-full">
                    <div class="flex items-center gap-3 mb-1">

                        {{-- Task Title --}}
                        <h2 class="text-lg font-semibold text-white">{{ strtoupper($task->name) }}</h2>

                        {{-- Priority Badge --}}
                        <span class="
                            px-2 py-1 rounded text-xs font-semibold
                            @if($task->priority === 'High') bg-red-600/40 text-red-300
                            @elseif($task->priority === 'Medium') bg-yellow-600/40 text-yellow-300
                            @else bg-green-600/40 text-green-300 @endif
                        ">
                            {{ $task->priority }}
                        </span>

                        {{-- Status Badge --}}
                        <span class="
                            px-2 py-1 rounded text-xs font-semibold
                            @if($task->is_done) bg-green-700/40 text-green-300
                            @else bg-red-700/40 text-red-300 @endif
                        ">
                            {{ $task->is_done ? 'Done' : 'Belum Dikerjakan' }}
                        </span>

                    </div>

                    {{-- Description --}}
                    <p class="text-gray-300 text-sm mb-3">
                        {{ $task->description ?: '-' }}
                    </p>

                    {{-- INFO ROW --}}
                    <div class="text-xs text-gray-400 space-y-1">
                        <div>
                            <strong>Created:</strong>
                            {{ $task->created_at->format('d M Y H:i') }}
                        </div>

                        <div>
                            <strong>Due:</strong>
                            @if($task->due_date)
                                <span class="text-purple-300 font-semibold">
                                    {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-gray-500">Tidak ada deadline</span>
                            @endif
                        </div>

                        <div>
                            <strong>Status:</strong>
                            {{ $task->is_done ? 'Completed' : 'To Do' }}
                        </div>

                        <div>
                            <strong>Progress:</strong>
                            {{ $task->subtasks->count() > 0 
                                ? round(($task->subtasks->where('is_done', true)->count() / $task->subtasks->count()) * 100) . '%'
                                : '0%' }}
                        </div>
                    </div>

                </div>


                {{-- RIGHT: BUTTONS --}}
                <div class="flex flex-col gap-2 w-full md:w-auto">

                    {{-- Toggle done --}}
                    <form action="{{ route('tasks.toggle', $task->id) }}" method="POST">
                        @csrf
                        <button class="w-full md:w-24 px-4 py-2 rounded bg-purple-600 hover:bg-purple-700 text-white">
                            {{ $task->is_done ? 'Un-Done' : 'Done' }}
                        </button>
                    </form>

                    {{-- Edit --}}
                    <a href="{{ route('tasks.edit', $task->id) }}" 
                       class="w-full md:w-24 text-center px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">
                        Edit
                    </a>

                    {{-- Delete --}}
                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                        onsubmit="return confirm('Hapus tugas ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full md:w-24 px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white">
                            Delete
                        </button>
                    </form>

                </div>

            </div>

        </div>
    @empty
        <p class="text-gray-400">Belum ada tugas.</p>
    @endforelse

</div>

@endsection
