<?php

namespace App\Http\Controllers;

use App\Events\PostCreated;
use App\Http\Requests\StorePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['index', 'show']),
        ];
    }

    public function index(): View
    {
        $posts = Post::with(['category', 'tags'])->latest()->paginate(6);

        return view('posts.index', ['posts' => $posts]);
    }

    public function create(): View
    {
        return view('posts.create', [
            'categories' => Category::all(),
            'tags' => Tag::all(),
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = $request->user()->posts()->create([
            ...$request->safe()->only(['category_id', 'title', 'short_content', 'content']),
            // store() generates a hashed, collision-free file name.
            'photo' => $request->file('photo')?->store('post-photos', 'public'),
        ]);

        $post->tags()->sync($request->input('tags', []));

        PostCreated::dispatch($post);

        return redirect()->route('posts.index');
    }

    public function show(Post $post): View
    {
        $post->load(['user', 'category', 'tags', 'comments.user']);

        return view('posts.show', [
            'post' => $post,
            'recent_posts' => Post::with('category')
                ->whereKeyNot($post->id)
                ->latest()
                ->take(5)
                ->get(),
            'categories' => Category::all(),
            'tags' => Tag::all(),
        ]);
    }

    public function edit(Post $post): View
    {
        Gate::authorize('update', $post);

        return view('posts.edit', [
            'post' => $post->load('tags'),
            'categories' => Category::all(),
            'tags' => Tag::all(),
        ]);
    }

    public function update(StorePostRequest $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $data = $request->safe()->only(['category_id', 'title', 'short_content', 'content']);

        if ($request->hasFile('photo')) {
            if ($post->photo) {
                Storage::disk('public')->delete($post->photo);
            }

            $data['photo'] = $request->file('photo')->store('post-photos', 'public');
        }

        $post->update($data);
        $post->tags()->sync($request->input('tags', []));

        return redirect()->route('posts.show', $post);
    }

    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        // Soft delete: the row is kept (restorable), so the photo is kept too.
        $post->delete();

        return redirect()->route('posts.index');
    }
}
