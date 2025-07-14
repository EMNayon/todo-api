<?php
namespace App\Services;

use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

class TodoService
{
    public function store(array $data): Todo
    {
        // dd($data);
        return Auth::user()->todos()->create($data);
    }

    public function update(Todo $todo, array $data): Todo
    {
        $todo->update($data);
        return $todo;
    }

    public function delete(Todo $todo): bool
    {
        return $todo->delete();
    }
}
