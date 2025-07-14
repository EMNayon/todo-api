<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ApplyAllTodoQueryScopes
{
    public function applyAllTodoQueryScopes($query, Request $request)
{
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', "%{$request->search}%")
              ->orWhere('description', 'like', "%{$request->search}%");
        });
    }

    if ($request->filled('due_date')) {
        $query->whereDate('due_date', $request->input('due_date'));
    }

    if ($request->has('is_completed')) {
        $query->where('is_completed', filter_var($request->input('is_completed'), FILTER_VALIDATE_BOOLEAN));
    }

    return $query;
}

}
