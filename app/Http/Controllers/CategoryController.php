<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // Return categories belonging to the authenticated user
        return $request->user()->categories()->select('id', 'name')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = $request->user()->categories()->create([
            'name' => $request->name,
        ]);

        return response()->json($category, 201);
    }
}
