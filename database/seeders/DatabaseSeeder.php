<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Category;

class SampleDataSeeder extends Seeder {
    public function run() {
        $c1 = Category::create(['name'=>'School']);
        $c2 = Category::create(['name'=>'Project']);

        $t1 = Task::create([
            'name'=>'TUGAS GAME ROBLOX',
            'description'=>'GAME ROBLOX',
            'priority'=>'High',
            'due_date'=>now()->addDays(1)->toDateString(),
            'category_id'=>$c1->id
        ]);
        $t1->subtasks()->createMany([
            ['title'=>'Bagian A'], ['title'=>'Bagian B']
        ]);

        $t2 = Task::create([
            'name'=>'BASIS DATA',
            'description'=>'MEMBUAT APLIKASI TODOLIST',
            'priority'=>'Low',
            'due_date'=>now()->addDays(2)->toDateString(),
            'category_id'=>$c2->id
        ]);
    }
}
