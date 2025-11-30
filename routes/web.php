<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CategoryController;

Route::get('/', function(){ return redirect()->route('dashboard'); });

Route::get('/dashboard', [TaskController::class,'dashboard'])->name('dashboard');

Route::resource('tasks', TaskController::class);
Route::post('/tasks/{task}/toggle', [TaskController::class,'toggle'])->name('tasks.toggle');
Route::get('/tasks/filter/{type}', [TaskController::class,'filter'])->name('tasks.filter');

Route::post('/tasks/{task}/subtasks', [TaskController::class,'addSubtask'])->name('tasks.subtasks.store');
Route::patch('/subtasks/{subtask}/toggle', [TaskController::class,'toggleSubtask'])->name('subtasks.toggle');

Route::resource('categories', CategoryController::class)->only(['index','store','destroy']);
