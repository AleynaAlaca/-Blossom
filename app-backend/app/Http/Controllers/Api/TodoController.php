<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Todo::latest()->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:task,payment'],
            'category' => ['nullable', 'required_if:type,task', 'in:work,home,school'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'amount' => ['nullable', 'required_if:type,payment', 'numeric', 'min:0'],
        ]);

        $todo = Todo::create([
            ...$validated,
            'title' => trim($validated['title']),
            'completed' => false,
        ]);

        return response()->json($todo, 201);
    }

    public function update(Request $request, Todo $todo): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'completed' => ['sometimes', 'boolean'],
            'category' => ['sometimes', 'nullable', 'in:work,home,school'],
            'priority' => ['sometimes', 'nullable', 'in:low,medium,high'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ]);

        if (array_key_exists('title', $validated)) {
            $validated['title'] = trim($validated['title']);
        }

        $todo->update($validated);

        return response()->json($todo->fresh());
    }

    public function destroy(Todo $todo): JsonResponse
    {
        $todo->delete();

        return response()->json(null, 204);
    }
}
