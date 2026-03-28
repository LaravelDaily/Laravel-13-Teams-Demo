<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Team $currentTeam): View
    {
        $categories = Category::withCount('posts')->latest()->paginate(10);

        return view('categories.index', compact('categories'));
    }

    public function create(Team $currentTeam): View
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request, Team $currentTeam): RedirectResponse
    {
        Category::create([
            'team_id' => $request->user()->current_team_id,
            'name' => $request->validated('name'),
            'slug' => Str::slug($request->validated('name')),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created.');
    }

    public function edit(Team $currentTeam, Category $category): View
    {
        Gate::authorize('update', $category);

        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Team $currentTeam, Category $category): RedirectResponse
    {
        $category->update([
            'name' => $request->validated('name'),
            'slug' => Str::slug($request->validated('name')),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Team $currentTeam, Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted.');
    }
}
