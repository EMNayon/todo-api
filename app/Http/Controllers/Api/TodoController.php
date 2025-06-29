<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todos = Todo::all();
        return response()->json($todos, 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->all();
        $todo = Todo::create($request->all());
        return response()->json($todo , 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Todo::find($id) ?? response(['message', 'Sorry, the requested todo could not be found.'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $todo = Todo::find($id);
        if(!$todo) return response(['message' , 'Sorry, the requested todo could not be found.']);
        $todo->update($request->only('title', 'description' , 'due_date', 'is_completed'));
        return response()->json($todo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $todo = Todo::find($id);
        if(!$todo) return response(['message', 'Sorry, the requested todo could not be found.'], 404);
        $todo->delete();
        return response(['message', 'The requested todo deleted']);
    }
}
