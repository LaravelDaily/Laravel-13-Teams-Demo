<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Team $currentTeam): View
    {
        $posts = Post::with('category')->latest()->paginate(10);

        return view('posts.index', compact('posts'));
    }

    public function create(Team $currentTeam): View
    {
        $categories = Category::orderBy('name')->get();

        return view('posts.create', compact('categories'));
    }

    public function store(StorePostRequest $request, Team $currentTeam): RedirectResponse
    {
        Post::create([
            'team_id' => $request->user()->current_team_id,
            ...$request->validated(),
        ]);

        return redirect()->route('posts.index')->with('success', 'Post created.');
    }

    public function edit(Team $currentTeam, Post $post): View
    {
        Gate::authorize('update', $post);

        $categories = Category::orderBy('name')->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(UpdatePostRequest $request, Team $currentTeam, Post $post): RedirectResponse
    {
        $post->update($request->validated());

        return redirect()->route('posts.index')->with('success', 'Post updated.');
    }

    public function destroy(Team $currentTeam, Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted.');
    }
}
