@extends('layouts.app')
@section('title','Edit Tugas')
@section('content')

<h2 class="text-2xl font-semibold mb-4">Edit Tugas</h2>

<div class="glass p-6 rounded">
  <form method="POST" action="{{ route('tasks.update',$task) }}">
    @csrf @method('PUT')

    <label class="block mb-2">Nama Tugas *</label>
    <input name="name" value="{{ $task->name }}" class="w-full p-3 rounded glass mb-3" required>

    <label class="block mb-2">Deskripsi</label>
    <textarea name="description" class="w-full p-3 rounded glass mb-3">{{ $task->description }}</textarea>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block mb-2">Prioritas</label>
        <select name="priority" class="w-full p-3 rounded glass">
          <option value="Low" {{ $task->priority=='Low'?'selected':'' }}>Low</option>
          <option value="Medium" {{ $task->priority=='Medium'?'selected':'' }}>Medium</option>
          <option value="High" {{ $task->priority=='High'?'selected':'' }}>High</option>
        </select>
      </div>

      <div>
        <label class="block mb-2">Tenggat Waktu</label>
        <input type="date" name="due_date" value="{{ $task->due_date?->format('Y-m-d') }}" class="w-full p-3 rounded glass">
      </div>
    </div>

    <label class="block mt-3 mb-2">Kategori</label>
    <select name="category_id" class="w-full p-3 rounded glass mb-4">
      <option value="">- None -</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ $cat->id == $task->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
      @endforeach
    </select>

    <button class="purple-btn">Update</button>
    <a href="{{ route('tasks.index') }}" class="ml-3 muted">Batal</a>
  </form>
</div>

@endsection
