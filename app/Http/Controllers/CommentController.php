<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
            'post_id' => ['required', 'integer', 'exists:posts,id'],
        ]);

        Comment::create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->back();
    }
}
