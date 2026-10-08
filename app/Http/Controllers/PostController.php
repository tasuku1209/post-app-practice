<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'category'])->latest()->get();

        return view('posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        $post->load(['user', 'category']);

        // 同じ秒に作られたリプライでも順番が決まるように、id でも並べる
        $replies = $post->replies()->with('user')->latest()->latest('id')->get();

        return view('posts.show', compact('post', 'replies'));
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        $categories = Category::orderBy('id')->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        // ブラウザは改行を CRLF で送るので、画面の字数と合うように LF にそろえてから数える
        $request->merge([
            'content' => str_replace("\r\n", "\n", (string) $request->input('content')),
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:140',
            'category_id' => 'required|exists:categories,id',
        ], [
            'content.required' => '本文を入力してください。',
            'content.max' => '本文は140字以内で入力してください。',
        ]);

        $post->update($validated);

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index');
    }
}
