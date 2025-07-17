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

/**
 * @OA\Schema(
 *     schema="Todo",
 *     type="object",
 *     title="Todo",
 *     required={"id", "title"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Buy milk"),
 *     @OA\Property(property="description", type="string", example="Get it from the local shop"),
 *     @OA\Property(property="is_completed", type="boolean", example=false),
 *     @OA\Property(property="due_date", type="string", format="date", example="2025-07-14"),
 *     @OA\Property(property="user_id", type="integer", example=5),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Tag(
 *     name="Todos",
 *     description="API Endpoints for managing todos"
 * )
 */
class TodoController extends Controller
{
    use ApplyAllTodoQueryScopes;

    public function __construct(protected TodoService $todoService) {}

    /**
     * @OA\Get(
     *     path="/api/todos",
     *     operationId="getTodos",
     *     tags={"Todos"},
     *     summary="Get list of todos",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Todo"))
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $todos = $request->user()->todos()->latest();
        $todos = $this->applyAllTodoQueryScopes($todos, $request)->paginate(5);

        if ($todos->isEmpty()) {
            return response()->json(['message' => 'No todos found'], 404);
        }

        return TodoResource::collection($todos);
    }

    /**
     * @OA\Post(
     *     path="/api/todos",
     *     operationId="storeTodo",
     *     tags={"Todos"},
     *     summary="Store a new todo",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="is_completed", type="boolean"),
     *             @OA\Property(property="due_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Todo")
     *     ),
     *     @OA\Response(response=400, description="Invalid input")
     * )
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
     * @OA\Get(
     *     path="/api/todos/{id}",
     *     operationId="showTodo",
     *     tags={"Todos"},
     *     summary="Get a specific todo",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="#/components/schemas/Todo")),
     *     @OA\Response(response=404, description="Todo not found")
     * )
     */
    public function show(Request $request, Todo $todo)
    {
        $todo = $request->user()->todos()->find($todo->id);

        if (! $todo) {
            return response()->json(['message' => 'Sorry, the requested todo could not be found.'], 404);
        }

        return new TodoResource($todo);
    }

    /**
     * @OA\Put(
     *     path="/api/todos/{id}",
     *     operationId="updateTodo",
     *     tags={"Todos"},
     *     summary="Update a specific todo",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="is_completed", type="boolean"),
     *             @OA\Property(property="due_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated successfully", @OA\JsonContent(ref="#/components/schemas/Todo")),
     *     @OA\Response(response=404, description="Todo not found")
     * )
     */
    public function update(UpdateTodoRequest $request, Todo $todo)
    {
        $todo = $this->todoService->update($todo, $request->validated());

        if (! $todo) {
            return response()->json(['message' => 'Sorry, the requested todo could not be found.'], 404);
        }

        return new TodoResource($todo);
    }

    /**
     * @OA\Delete(
     *     path="/api/todos/{id}",
     *     operationId="deleteTodo",
     *     tags={"Todos"},
     *     summary="Delete a specific todo",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Todo deleted"),
     *     @OA\Response(response=404, description="Todo not found")
     * )
     */
    public function destroy(Todo $todo, Request $request)
    {
        $deleted = $this->todoService->delete($todo);

        if (! $deleted) {
            return response()->json(['message' => 'Todo could not be deleted'], 404);
        }

        return response()->json(['message' => 'The requested todo has been deleted successfully.'], 200);
    }
}
