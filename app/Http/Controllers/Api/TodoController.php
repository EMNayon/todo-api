<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Http\Resources\TodoResource;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $todos = $request->user()->todos()->latest();
        if ($request->has('search')) {
            $todos = $todos->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Filter by title
        if ($request->filled('due_date')) {
            $todos->whereDate('due_date', $request->input('due_date'));
        }

        // Filter by is_complete (1 or 0)
        if ($request->has('is_completed')) {
            $todos->where('is_completed', $request->boolean('is_completed'));
        }

        $todos = $todos->paginate(5);

        if ($todos->isEmpty()) {
            return response()->json(['message' => 'No todos found'], 404);
        }
        // return response()->json($todos, 200);
        return TodoResource::collection($todos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request)
    {
        $todo = $request->user()->todos()->create($request->validated());
        if (! $todo) {
            return response()->json(['message' => 'Todo could not be created'], 500);
        }
        // return response()->json($todo , 201);
        return (new TodoResource($todo))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        // $todo = Todo::find($id);
        $todo = $request->user()->todos()->find($id);
        if (! $todo) {
            return response()->json(['message', 'Sorry, the requested todo could not be found.'], 404);
        }
        // abort_if(! $todo, 404, 'Sorry, the requested todo could not be found.');
        // return response()->json($todo, 200);
        return new TodoResource($todo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoRequest $request, string $id)
    {
        // $todo = Todo::find($id);
        $todo = $request->user()->todos()->find($id);
        if (! $todo) {
            return response(['message', 'Sorry, the requested todo could not be found.'], 404);
        }

        $todo->update($request->validated());
        // return response()->json($todo, 200);
        return new TodoResource($todo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, Request $request)
    {
        // $todo = Todo::find($id);
        $todo = $request->user()->todos()->find($id);
        if (! $todo) {
            return response(['message', 'Sorry, the requested todo could not be found.'], 404);
        }

        $todo->delete();
        return response(['message', 'The requested todo deleted'], 200);
    }
}
