<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Reply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostEditedLabelTest extends TestCase
{
    use RefreshDatabase;

    // 1 時間前に作られ、そのあと一度も編集されていないポストを作る
    private function 未編集のポストを作る(array $attributes = []): Post
    {
        return Post::factory()->create(array_merge([
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ], $attributes));
    }

    public function test_一度も編集していないポストには編集済みが出ない(): void
    {
        $post = $this->未編集のポストを作る(['title' => '未編集のタイトル']);

        $this->actingAs(User::factory()->create())
            ->get(route('posts.index'))
            ->assertOk()
            ->assertSee('未編集のタイトル')
            ->assertDontSee('編集済み');
    }

    public function test_自分のポストを編集して保存するとタイムラインに編集済みが出る(): void
    {
        $user = User::factory()->create();
        $post = $this->未編集のポストを作る(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('posts.update', $post), [
            'title' => '編集後のタイトル',
            'content' => '編集後の本文',
            'category_id' => $post->category_id,
        ]);

        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => '編集後のタイトル']);
        $this->actingAs($user)
            ->get(route('posts.index'))
            ->assertSee('編集後のタイトル')
            ->assertSee('編集済み');
    }

    public function test_編集済みが出るのは編集したポストだけ(): void
    {
        $user = User::factory()->create();
        $edited = $this->未編集のポストを作る(['user_id' => $user->id, 'title' => '編集するポスト']);
        $this->未編集のポストを作る(['user_id' => $user->id, 'title' => '編集しないポスト']);

        $this->actingAs($user)->put(route('posts.update', $edited), [
            'title' => '編集するポスト（直した）',
            'content' => '直した本文',
            'category_id' => $edited->category_id,
        ]);

        $response = $this->actingAs($user)->get(route('posts.index'));

        // 画面に「編集済み」が出るのは 1 件だけ
        $this->assertSame(1, substr_count($response->getContent(), '編集済み'));

        // しかも、それは編集したポストの枠の中にある（「編集済み」はタイトルより前に出る）
        $articles = array_filter(
            explode('<article', $response->getContent()),
            fn (string $html) => str_contains($html, 'class="post"')
        );
        $this->assertCount(2, $articles);
        foreach ($articles as $html) {
            if (str_contains($html, '編集するポスト（直した）')) {
                $this->assertStringContainsString('編集済み', $html);
            } else {
                $this->assertStringContainsString('編集しないポスト', $html);
                $this->assertStringNotContainsString('編集済み', $html);
            }
        }
    }

    public function test_他人のポストを編集しようとしても編集済みにならない(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = $this->未編集のポストを作る(['user_id' => $owner->id, 'title' => '元のタイトル']);

        $this->actingAs($other)->put(route('posts.update', $post), [
            'title' => '書き換えようとしたタイトル',
            'content' => '書き換えようとした本文',
            'category_id' => $post->category_id,
        ])->assertForbidden();

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'title' => '元のタイトル']);
        $this->actingAs($owner)
            ->get(route('posts.index'))
            ->assertSee('元のタイトル')
            ->assertDontSee('編集済み');
    }

    public function test_リプライが付いただけでは編集済みにならない(): void
    {
        $user = User::factory()->create();
        $post = $this->未編集のポストを作る();

        $this->actingAs($user)
            ->post(route('replies.store', $post), ['content' => 'リプライです'])
            ->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseCount('replies', 1);

        $this->actingAs($user)
            ->get(route('posts.index'))
            ->assertDontSee('編集済み');
    }

    public function test_編集済みが出ても一覧の編集と削除は今までどおり動く(): void
    {
        $user = User::factory()->create();
        $mine = $this->未編集のポストを作る(['user_id' => $user->id]);
        $others = $this->未編集のポストを作る();
        $mine->update(['title' => '直したタイトル']);

        $response = $this->actingAs($user)->get(route('posts.index'));

        // 編集・削除のボタンは、自分のポストにだけ出る
        $response->assertSee(route('posts.edit', $mine), false);
        // 削除の URL は詳細（返信リンク）と同じなので、削除フォームの action で確かめる
        $response->assertSee('action="'.route('posts.destroy', $mine).'"', false);
        $response->assertDontSee(route('posts.edit', $others), false);
        $response->assertDontSee('action="'.route('posts.destroy', $others).'"', false);

        $this->actingAs($user)
            ->delete(route('posts.destroy', $mine))
            ->assertRedirect(route('posts.index'));
        $this->assertModelMissing($mine);
        $this->assertModelExists($others);
    }

    public function test_未ログインで一覧を開くとログイン画面へ移動する(): void
    {
        $this->get(route('posts.index'))->assertRedirect(route('login'));
    }
}
