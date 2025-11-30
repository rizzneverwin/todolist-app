@extends('layouts.app')

@section('title','Tambah Tugas')

@section('content')

<div class="max-w-4xl mx-auto mt-10 glass rounded-2xl p-8 border border-white/20">

    <h2 class="text-2xl font-bold mb-6">Tambah Tugas Baru</h2>

    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf

        {{-- NAMA TUGAS --}}
        <div class="mb-6">
            <label class="block mb-1 font-semibold">Nama Tugas *</label>
            <input 
                type="text" 
                name="name" 
                required
                class="w-full p-3 rounded-xl bg-[#1e1f25] border-2 border-white/20 
                       focus:border-purple-500 outline-none text-white placeholder-gray-400"
                placeholder="Masukkan nama tugas"
            >
        </div>

        {{-- DESKRIPSI --}}
        <div class="mb-6">
            <label class="block mb-1 font-semibold">Deskripsi</label>
            <textarea
                name="description"
                class="w-full p-3 rounded-xl bg-[#1e1f25] border-2 border-white/20 
                       focus:border-purple-500 outline-none text-white placeholder-gray-400"
                rows="3"
                placeholder="Tulis deskripsi tugas (opsional)"
            ></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- PRIORITAS --}}
            <div>
                <label class="block mb-1 font-semibold">Prioritas</label>

                <select 
                    name="priority"
                    class="w-full p-3 rounded-xl bg-[#1e1f25] border-2 border-white/20 
                           focus:border-purple-500 outline-none text-white cursor-pointer"
                >
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>

            {{-- DUE DATE --}}
            <div>
                <label class="block mb-1 font-semibold">Tenggat Waktu</label>
                <input 
                    type="date" 
                    name="due_date"
                    class="w-full p-3 rounded-xl bg-[#1e1f25] border-2 border-white/20 
                           focus:border-purple-500 outline-none text-white cursor-pointer"
                >
            </div>

        </div>

        {{-- BUTTON --}}
        <div class="mt-8 flex gap-4">
            <button 
                class="px-6 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-semibold">
                Simpan
            </button>

            <a href="{{ route('tasks.index') }}" 
               class="px-6 py-3 rounded-xl bg-gray-600 hover:bg-gray-700 text-white font-semibold">
                Batal
            </a>
        </div>

    </form>
</div>

@endsection
