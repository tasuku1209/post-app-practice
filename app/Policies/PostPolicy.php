<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    // いいねは、ログインしている人なら誰のポストにでもできる（自分のポストも含む）
    public function like(User $user, Post $post): bool
    {
        return true;
    }
}
