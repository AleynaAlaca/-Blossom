<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    // GET /api/todos
    public function index()
    {
        return Todo::all();
    }

    // POST /api/todos
    public function store(Request $request)
    {
        $todo = Todo::create([
            'title' => $request->title,
            'completed' => false
        ]);

        return $todo;
    }

    // PUT /api/todos/{id}
    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);

        $todo->update([
            'completed' => $request->completed
        ]);

        return $todo;
    }

    // DELETE /api/todos/{id}
    public function destroy($id)
    {
        return Todo::destroy($id);
    }
}