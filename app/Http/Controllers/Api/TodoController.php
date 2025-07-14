<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use App\Services\TodoService;
use App\Traits\ApplyAllTodoQueryScopes;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    // use HasTodoFilters, HasTodoSearch;
    use ApplyAllTodoQueryScopes;

    /**
     * Create a new controller instance.
     */
    // protected $todoService;
    // public function __construct( TodoService $todoService ){}
    public function __construct(protected TodoService $todoService)
    {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $todos = $request->user()->todos()->latest();

        $todos = $this->applyAllTodoQueryScopes($todos, $request);

        $todos = $todos->paginate(5);

        if ($todos->isEmpty()) {
            return response()->json(['message' => 'No todos found'], 404);
        }
        return TodoResource::collection($todos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request)
    {
        $todo = $this->todoService->store($request->validated());
        if (! $todo) {
            return response()->json(['message' => 'Todo could not be created'], 500);
        }
        return (new TodoResource($todo))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Todo $todo)
    {
        $todo = $request->user()->todos()->find($todo);
        if (! $todo) {
            return response()->json(['message', 'Sorry, the requested todo could not be found.'], 404);
        }
        return new TodoResource($todo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoRequest $request, Todo $todo)
    {
        $todo = $this->todoService->update($todo, $request->validated());
        if (! $todo) {
            return response(['message', 'Sorry, the requested todo could not be found.'], 404);
        }

        $todo->update($request->validated());
        return new TodoResource($todo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo, Request $request)
    {
        $todo = $this->todoService->delete($todo);

        return response()->json(['message' => 'The requested todo has been deleted successfully.'], 200);
    }
}
