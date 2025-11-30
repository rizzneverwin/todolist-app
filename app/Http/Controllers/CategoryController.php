<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller {
    public function index(){
        $categories = Category::withCount('tasks')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $r){
        $r->validate(['name'=>'required|string|max:100']);
        Category::create(['name'=>$r->input('name')]);
        return back()->with('success','Category added');
    }

    public function destroy(Category $category){
        $category->delete();
        return back()->with('success','Deleted');
    }
}
