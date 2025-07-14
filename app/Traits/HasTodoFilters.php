<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasTodoFilters
{
    public function applyTodoFilters($query, Request $request)
    {

        // Filter by due_date
        if ($request->filled('due_date')) {
            $query->whereDate('due_date', $request->input('due_date'));
        }

        // Filter by is_completed (boolean)
        if ($request->has('is_completed')) {
            $query->where('is_completed', filter_var($request->input('is_completed'), FILTER_VALIDATE_BOOLEAN));
        }

        return $query;
    }
}
