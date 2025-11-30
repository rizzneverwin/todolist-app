<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Subtask;
use App\Models\Category;
use Carbon\Carbon;

class TaskController extends Controller {

    public function dashboard(){
        $total = Task::count();
        $done = Task::where('is_done', true)->count();
        $pending = Task::where('is_done', false)->count();

        $todayCount = Task::whereDate('due_date', now()->toDateString())->count();

        $overdueCount = Task::where('is_done', false)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->count();

        $allTasks = Task::with('subtasks')->get();
        $totalSubtasks = 0; $completedSubtasks = 0;
        foreach($allTasks as $t){
            $totalSubtasks += $t->subtasks->count();
            $completedSubtasks += $t->subtasks->where('is_done', true)->count();
        }
        $avgSubtaskProgress = $totalSubtasks>0 ? round(($completedSubtasks/$totalSubtasks)*100) : 0;

        $upcoming = Task::whereNotNull('due_date')
            ->whereDate('due_date','>=', now()->toDateString())
            ->orderBy('due_date')
            ->limit(6)->get();

        $categories = Category::withCount('tasks')->get();

        $chartData = [
            'labels' => ['Selesai','Belum'],
            'values' => [$done, $pending]
        ];

        return view('tasks.dashboard', compact(
            'total','done','pending',
            'todayCount','overdueCount','avgSubtaskProgress',
            'upcoming','categories','chartData'
        ));
    }

    public function index(){
        $tasks = Task::with('category','subtasks')->orderBy('is_done')->orderBy('due_date')->get();
        return view('tasks.index', compact('tasks'));
    }

    public function create(){
        $categories = Category::all();
        return view('tasks.create', compact('categories'));
    }

    public function store(Request $r){
        $data = $r->validate([
            'name'=>'required|string|max:255',
            'description'=>'nullable|string',
            'priority'=>'nullable|in:Low,Medium,High',
            'due_date'=>'nullable|date',
            'category_id'=>'nullable|exists:categories,id'
        ]);
        $data['priority'] = $data['priority'] ?? 'Medium';
        $task = Task::create($data);

        if($r->filled('subtasks') && is_array($r->input('subtasks'))){
            foreach($r->input('subtasks') as $st){
                if(trim($st)) $task->subtasks()->create(['title'=>$st]);
            }
        }

        return redirect()->route('tasks.index')->with('success','Tugas dibuat');
    }

    public function edit(Task $task){
        $categories = Category::all();
        $task->load('subtasks');
        return view('tasks.edit', compact('task','categories'));
    }

    public function update(Request $r, Task $task){
        $data = $r->validate([
            'name'=>'required|string|max:255',
            'description'=>'nullable|string',
            'priority'=>'nullable|in:Low,Medium,High',
            'due_date'=>'nullable|date',
            'category_id'=>'nullable|exists:categories,id',
            'status'=>'nullable|in:todo,in_progress,review,done'
        ]);
        $task->update($data);
        return redirect()->route('tasks.index')->with('success','Tugas diupdate');
    }

    public function destroy(Task $task){
        $task->delete();
        return redirect()->route('tasks.index')->with('success','Tugas dihapus');
    }

    public function toggle(Task $task){
        $task->is_done = !$task->is_done;
        $task->save();
        return back()->with('success','Status diubah');
    }

    public function addSubtask(Request $r, Task $task){
        $r->validate(['title'=>'required|string|max:255']);
        $task->subtasks()->create(['title'=>$r->input('title')]);
        return back();
    }

    public function toggleSubtask(Subtask $subtask){
        $subtask->is_done = !$subtask->is_done;
        $subtask->save();
        return back();
    }

    public function filter($type){
        if($type==='done') $tasks = Task::where('is_done', true)->get();
        elseif($type==='pending') $tasks = Task::where('is_done', false)->get();
        else $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }
}
