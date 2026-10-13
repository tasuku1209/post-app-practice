<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    // いいねと取り消しは同じ URL（メソッドだけ違う）なので、action では押した状態を見分けられない。
    // ポストごとの <article> を取り出して、その中のボタンの状態で確かめる
    private function articleOf(TestResponse $response, Post $post): string
    {
        $found = preg_match('#<article class="post" id="post-'.$post->id.'">.*?</article>#s', $response->getContent(), $matches);
        $this->assertSame(1, $found, "ポスト {$post->id} がタイムラインに出ていません。");

        return $matches[0];
    }

    // 押した状態: 押した見た目の印が付き、次に押すと取り消す（DELETE）フォームになる
    private function assertPressed(string $article, int $count): void
    {
        $this->assertStringContainsString('class="like-btn liked" aria-pressed="true"', $article);
        $this->assertStringContainsString('name="_method" value="DELETE"', $article);
        $this->assertStringContainsString('<span class="like-count">'.$count.'</span>', $article);
    }

    // 押していない状態: 押した見た目の印は無く、取り消し（DELETE）のフォームでもない
    private function assertNotPressed(string $article, int $count): void
    {
        $this->assertStringContainsString('aria-pressed="false"', $article);
        $this->assertStringNotContainsString('like-btn liked', $article);
        $this->assertStringNotContainsString('name="_method" value="DELETE"', $article);
        $this->assertStringContainsString('<span class="like-count">'.$count.'</span>', $article);
    }

    public function test_タイムラインの各ポストにいいねのボタンと今の数が出る(): void
    {
        $me = User::factory()->create();
        $popular = Post::factory()->create(['created_at' => now()->subHour()]);
        $quiet = Post::factory()->create(['created_at' => now()]);
        $popular->likedUsers()->attach(User::factory()->count(3)->create());

        $response = $this->actingAs($me)->get(route('posts.index'));

        $response->assertOk();
        // どちらのポストにもボタンがあり、数は 3 と 0（新しい順なので quiet が先）
        $response->assertSee('action="'.route('likes.store', $popular).'"', false);
        $response->assertSee('action="'.route('likes.store', $quiet).'"', false);
        $response->assertSeeInOrder([
            'action="'.route('likes.store', $quiet).'"',
            '<span class="like-count">0</span>',
            'action="'.route('likes.store', $popular).'"',
            '<span class="like-count">3</span>',
        ], false);
    }

    public function test_いいねしていないポストのボタンを押すと数が1つ増えて押した状態になる(): void
    {
        $me = User::factory()->create();
        $post = Post::factory()->create();
        $post->likedUsers()->attach(User::factory()->create());

        $response = $this->actingAs($me)->post(route('likes.store', $post));

        $response->assertRedirect(route('posts.index').'#post-'.$post->id);
        $this->assertDatabaseHas('likes', ['user_id' => $me->id, 'post_id' => $post->id]);
        $this->assertDatabaseCount('likes', 2);

        $response = $this->actingAs($me)->get(route('posts.index'));

        $this->assertPressed($this->articleOf($response, $post), 2);
    }

    public function test_押した状態のボタンをもう一度押すと数が1つ減って押していない状態に戻る(): void
    {
        $me = User::factory()->create();
        $post = Post::factory()->create();
        $post->likedUsers()->attach([$me->id, User::factory()->create()->id]);

        $response = $this->actingAs($me)->delete(route('likes.destroy', $post));

        $response->assertRedirect(route('posts.index').'#post-'.$post->id);
        $this->assertDatabaseMissing('likes', ['user_id' => $me->id, 'post_id' => $post->id]);
        $this->assertDatabaseCount('likes', 1);

        $response = $this->actingAs($me)->get(route('posts.index'));

        $this->assertNotPressed($this->articleOf($response, $post), 1);
    }

    public function test_別のアカウントでは自分が押していないポストは押していない見た目のまま数だけが増えている(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $post = Post::factory()->create();
        $other = Post::factory()->create();

        $this->actingAs($a)->post(route('likes.store', $post));

        // A から見ると押した状態、数は 1
        $responseA = $this->actingAs($a)->get(route('posts.index'));
        $this->assertPressed($this->articleOf($responseA, $post), 1);
        // 誰も押していない別のポストは A から見ても押していないまま
        $this->assertNotPressed($this->articleOf($responseA, $other), 0);

        // 別のアカウントでログインし直す（B は押していない）
        $this->post(route('logout'));
        $this->assertGuest();

        $responseB = $this->actingAs($b)->get(route('posts.index'));
        $this->assertNotPressed($this->articleOf($responseB, $post), 1);
        $this->assertNotPressed($this->articleOf($responseB, $other), 0);
    }

    public function test_押した状態はポストごとに分かれる(): void
    {
        $me = User::factory()->create();
        $liked = Post::factory()->create();
        $notLiked = Post::factory()->create();
        $liked->likedUsers()->attach($me);

        $response = $this->actingAs($me)->get(route('posts.index'));

        $this->assertPressed($this->articleOf($response, $liked), 1);
        $this->assertNotPressed($this->articleOf($response, $notLiked), 0);
    }

    public function test_自分のポストにもいいねできる(): void
    {
        $me = User::factory()->create();
        $post = Post::factory()->for($me)->create();

        $this->actingAs($me)->post(route('likes.store', $post))->assertRedirect();

        $this->assertDatabaseHas('likes', ['user_id' => $me->id, 'post_id' => $post->id]);
    }

    public function test_同じポストに続けて2回いいねを送っても1件のまま(): void
    {
        $me = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($me)->post(route('likes.store', $post));
        $this->actingAs($me)->post(route('likes.store', $post))->assertRedirect();

        $this->assertDatabaseCount('likes', 1);
    }

    public function test_いいねしていないポストの取り消しを送っても数は変わらない(): void
    {
        $me = User::factory()->create();
        $post = Post::factory()->create();
        $post->likedUsers()->attach(User::factory()->create());

        $this->actingAs($me)->delete(route('likes.destroy', $post))->assertRedirect();

        $this->assertDatabaseCount('likes', 1);
    }

    public function test_自分のいいねを取り消しても他の人のいいねは残る(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create();
        $post->likedUsers()->attach([$me->id, $other->id]);

        $this->actingAs($me)->delete(route('likes.destroy', $post));

        $this->assertDatabaseHas('likes', ['user_id' => $other->id, 'post_id' => $post->id]);
    }

    public function test_ログインしていないといいねと取り消しはログイン画面へ移動する(): void
    {
        $post = Post::factory()->create();
        $liker = User::factory()->create();
        $post->likedUsers()->attach($liker);

        $this->post(route('likes.store', $post))->assertRedirect(route('login'));
        $this->delete(route('likes.destroy', $post))->assertRedirect(route('login'));

        // どちらもデータは変わらない
        $this->assertDatabaseCount('likes', 1);
    }

    public function test_存在しないポストへのいいねは404になる(): void
    {
        $me = User::factory()->create();

        $this->actingAs($me)->post(route('likes.store', 9999))->assertNotFound();
        $this->actingAs($me)->delete(route('likes.destroy', 9999))->assertNotFound();

        $this->assertDatabaseCount('likes', 0);
    }

    public function test_いいねを付け外ししても編集済みの目印は変わらない(): void
    {
        $me = User::factory()->create();
        $untouched = Post::factory()->create(['created_at' => now()->subDay(), 'updated_at' => now()->subDay()]);
        $edited = Post::factory()->create(['created_at' => now()->subDays(2), 'updated_at' => now()->subDay()]);

        $this->actingAs($me)->post(route('likes.store', $untouched));
        $this->actingAs($me)->post(route('likes.store', $edited));
        $this->actingAs($me)->delete(route('likes.destroy', $edited));

        $this->assertTrue($untouched->fresh()->updated_at->equalTo($untouched->created_at));
        $this->assertFalse($untouched->fresh()->isEdited());
        $this->assertTrue($edited->fresh()->isEdited());

        // 画面の「編集済み」は 1 件だけ（編集されたポスト側）
        $response = $this->actingAs($me)->get(route('posts.index'));
        $this->assertSame(1, substr_count($response->getContent(), '<span class="edited">編集済み</span>'));
    }

    public function test_ポストを削除するとそのいいねも消える(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->for($owner)->create();
        $post->likedUsers()->attach(User::factory()->count(2)->create());

        $this->actingAs($owner)->delete(route('posts.destroy', $post));

        $this->assertDatabaseCount('likes', 0);
    }

    public function test_ユーザーを削除するとそのユーザーのいいねも消える(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $post->likedUsers()->attach($user);

        $user->delete();

        $this->assertDatabaseCount('likes', 0);
        $this->assertModelExists($post);
    }

    public function test_タイムラインは今までどおりで一覧_編集_削除_返信が動く(): void
    {
        $me = User::factory()->create();
        $mine = Post::factory()->for($me)->create(['created_at' => now()]);
        $others = Post::factory()->create(['created_at' => now()->subHour()]);

        $response = $this->actingAs($me)->get(route('posts.index'));

        $response->assertOk();
        $response->assertSeeInOrder([$mine->title, $others->title]);
        $response->assertSee(route('posts.show', $mine), false);
        $response->assertSee(route('posts.show', $others), false);
        $response->assertSee(route('posts.edit', $mine), false);
        $response->assertDontSee(route('posts.edit', $others), false);
        $response->assertSee('action="'.route('posts.destroy', $mine).'"', false);
        $response->assertDontSee('action="'.route('posts.destroy', $others).'"', false);
    }
}
