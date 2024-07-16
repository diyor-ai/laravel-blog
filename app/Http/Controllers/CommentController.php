<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $comment = Comment::create([
            'body' => $request->body,
            'user_id' => 1,
            'post_id' => $request->post_id,
        ]);

        return redirect()->back();
    }
}
