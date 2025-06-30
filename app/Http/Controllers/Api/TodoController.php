<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\TodoResource;
use App\Models\Todo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todos = Todo::all();
        // return response()->json($todos, 200);
        return TodoResource::collection($todos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request)
    {
        // $request->all();
        $todo = Todo::create($request->validated());
        // return response()->json($todo , 201);
        return (new TodoResource($todo))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $todo = Todo::find($id);
        if(!$todo){
            return response()->json(['message', 'Sorry, the requested todo could not be found.'], 404);
        }
        // return response()->json($todo, 200);
        return new TodoResource($todo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoRequest $request, string $id)
    {
        $todo = Todo::find($id);
        if(!$todo) return response(['message' , 'Sorry, the requested todo could not be found.'] , 404);
        $todo->update($request->validated());
        // return response()->json($todo, 200);
        return new TodoResource($todo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $todo = Todo::find($id);
        if(!$todo) return response(['message', 'Sorry, the requested todo could not be found.'], 404);
        $todo->delete();
        return response(['message', 'The requested todo deleted'], 200);
    }
}
