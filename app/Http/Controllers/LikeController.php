<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $this->authorize('like', $post);

        // すでにいいね済みでも二重に増えないようにする
        $post->likedUsers()->syncWithoutDetaching([$request->user()->id]);

        return $this->backToTimeline($post);
    }

    public function destroy(Request $request, Post $post)
    {
        $this->authorize('like', $post);

        $post->likedUsers()->detach($request->user()->id);

        return $this->backToTimeline($post);
    }

    // 押したポストの位置に戻して、タイムラインの先頭までスクロールし直さなくて済むようにする
    private function backToTimeline(Post $post)
    {
        return redirect()->to(route('posts.index').'#post-'.$post->id);
    }
}
