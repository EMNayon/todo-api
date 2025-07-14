<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HasTodoSearch
{
    public function applyTodoSearch($query, Request $request)
    {
        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query;
    }
}
