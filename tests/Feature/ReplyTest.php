<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_ポスト詳細にそのポストのリプライだけが新しい順で出る(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $otherPost = Post::factory()->create();
        Reply::factory()->for($post)->create(['content' => '古いリプライ', 'created_at' => now()->subHour()]);
        Reply::factory()->for($post)->create(['content' => '新しいリプライ', 'created_at' => now()]);
        Reply::factory()->for($otherPost)->create(['content' => '別のポストへのリプライ']);

        $response = $this->actingAs($user)->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertSee($post->title);
        $response->assertSeeInOrder(['新しいリプライ', '古いリプライ']);
        $response->assertDontSee('別のポストへのリプライ');
    }

    public function test_リプライが0件のポストでは案内が出る(): void
    {
        $post = Post::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('posts.show', $post))
            ->assertOk()
            ->assertSee('まだリプライがありません。');
    }

    public function test_本文を入れて送るとリプライが増えて詳細画面に出る(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('replies.store', $post), ['content' => 'よろしくお願いします']);

        $response->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseHas('replies', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'よろしくお願いします',
        ]);
        $this->actingAs($user)
            ->get(route('posts.show', $post))
            ->assertSee('よろしくお願いします');
    }

    public function test_本文が空だとエラーが表示されてリプライは増えない(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->from(route('posts.show', $post))
            ->post(route('replies.store', $post), ['content' => '']);

        $response->assertRedirect(route('posts.show', $post));
        $response->assertSessionHasErrors(['content' => '本文を入力してください。']);
        $this->assertDatabaseCount('replies', 0);

        $this->actingAs($user)
            ->get(route('posts.show', $post))
            ->assertSee('本文を入力してください。');
    }

    public function test_本文が140字ちょうどなら増える(): void
    {
        $post = Post::factory()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('replies.store', $post), ['content' => str_repeat('あ', 140)])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('replies', 1);
    }

    public function test_本文が141字だとエラーになり増えない(): void
    {
        $post = Post::factory()->create();

        $this->actingAs(User::factory()->create())
            ->from(route('posts.show', $post))
            ->post(route('replies.store', $post), ['content' => str_repeat('あ', 141)])
            ->assertSessionHasErrors(['content' => '本文は140字以内で入力してください。']);

        $this->assertDatabaseCount('replies', 0);
    }

    public function test_改行は改行コードをそろえて字数を数える(): void
    {
        $post = Post::factory()->create();
        // CRLF のままだと 141 字、LF にそろえると 140 字
        $content = str_repeat('あ', 69)."\r\n".str_repeat('い', 70);

        $this->actingAs(User::factory()->create())
            ->post(route('replies.store', $post), ['content' => $content])
            ->assertSessionHasNoErrors();

        $this->assertSame(
            str_repeat('あ', 69)."\n".str_repeat('い', 70),
            Reply::first()->content
        );
    }

    public function test_エラーで戻ったとき入力途中の本文が残る(): void
    {
        $post = Post::factory()->create();
        $tooLong = str_repeat('あ', 141);

        $this->actingAs(User::factory()->create())
            ->from(route('posts.show', $post))
            ->post(route('replies.store', $post), ['content' => $tooLong]);

        $this->get(route('posts.show', $post))->assertSee($tooLong);
    }

    public function test_自分のリプライは削除できる(): void
    {
        $user = User::factory()->create();
        $reply = Reply::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete(route('replies.destroy', $reply))
            ->assertRedirect(route('posts.show', $reply->post_id));

        $this->assertModelMissing($reply);
    }

    public function test_他人のリプライは削除できない(): void
    {
        $reply = Reply::factory()->create();

        $this->actingAs(User::factory()->create())
            ->delete(route('replies.destroy', $reply))
            ->assertForbidden();

        $this->assertModelExists($reply);
    }

    public function test_ポストの持ち主でも他人のリプライは削除できない(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->for($owner)->create();
        $reply = Reply::factory()->for($post)->create();

        $this->actingAs($owner)
            ->delete(route('replies.destroy', $reply))
            ->assertForbidden();

        $this->assertModelExists($reply);
    }

    public function test_削除ボタンは自分のリプライにだけ出る(): void
    {
        $me = User::factory()->create();
        $post = Post::factory()->create();
        $mine = Reply::factory()->for($post)->for($me)->create();
        $others = Reply::factory()->for($post)->create();

        $response = $this->actingAs($me)->get(route('posts.show', $post));

        $response->assertSee(route('replies.destroy', $mine), false);
        $response->assertDontSee(route('replies.destroy', $others), false);
    }

    public function test_ログインしていないと詳細_送信_削除はログイン画面へ移動する(): void
    {
        $reply = Reply::factory()->create();
        $post = $reply->post;

        $this->get(route('posts.show', $post))->assertRedirect(route('login'));
        $this->post(route('replies.store', $post), ['content' => 'こんにちは'])->assertRedirect(route('login'));
        $this->delete(route('replies.destroy', $reply))->assertRedirect(route('login'));

        $this->assertDatabaseCount('replies', 1);
    }

    public function test_存在しないポストの詳細は404になる(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('posts.show', 9999))
            ->assertNotFound();
    }

    public function test_ポストを削除するとそのリプライも消える(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->for($owner)->create();
        $reply = Reply::factory()->for($post)->create();

        $this->actingAs($owner)->delete(route('posts.destroy', $post));

        $this->assertModelMissing($reply);
    }

    public function test_ユーザーを削除するとそのユーザーのリプライも消える(): void
    {
        $user = User::factory()->create();
        $reply = Reply::factory()->for($user)->create();

        $user->delete();

        $this->assertModelMissing($reply);
    }

    public function test_タイムラインは今までどおりで返信リンクが出る(): void
    {
        $me = User::factory()->create();
        $mine = Post::factory()->for($me)->create();
        $others = Post::factory()->create();

        $response = $this->actingAs($me)->get(route('posts.index'));

        $response->assertOk();
        $response->assertSee(route('posts.show', $mine), false);
        $response->assertSee(route('posts.show', $others), false);
        // 編集・削除は自分のポストにだけ出る
        $response->assertSee(route('posts.edit', $mine), false);
        $response->assertDontSee(route('posts.edit', $others), false);
        $response->assertSee('action="'.route('posts.destroy', $mine).'"', false);
        $response->assertDontSee('action="'.route('posts.destroy', $others).'"', false);
    }
}
