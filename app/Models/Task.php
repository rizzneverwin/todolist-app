<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model {
    use HasFactory;

    protected $fillable = ['name','description','priority','due_date','category_id','is_done'];

    protected $casts = [
        'due_date' => 'date',
        'is_done' => 'boolean',
    ];

    // relationships
    public function subtasks(){
        return $this->hasMany(Subtask::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    // helper
    public function statusLabel(){
        return $this->is_done ? 'Done' : 'To Do';
    }

    public function progressPercent(){
        $total = $this->subtasks()->count();
        if($total === 0) return 0;
        $done = $this->subtasks()->where('is_done', true)->count();
        return round(($done / $total) * 100);
    }
}
