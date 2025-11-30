<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subtask extends Model {
    use HasFactory;

    protected $fillable = ['task_id','title','is_done'];

    protected $casts = [
        'is_done' => 'boolean',
    ];

    public function task(){
        return $this->belongsTo(Task::class);
    }
}
