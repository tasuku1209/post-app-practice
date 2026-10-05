# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## プロジェクトの概要

`post-app` は、投稿の一覧・編集・削除ができる小さな Laravel 10 の教材アプリです（認証は Fortify）。Tutorial 13 から 15 まで、このアプリ 1 本を共通の題材として使います。README やコード内のコメントは日本語なので、画面の文言やドキュメントも日本語で書いてください。

- `answers/` は Tutorial 13 の設計書の解答例（Markdown と draw.io）です。アプリのコードとは関係ないので、アプリの作業では読んだり変更したりしないでください。
- `docs/` は受講者が自分の設計書を置く場所です（Tutorial 13 の中で作るので、まだ無いこともあります）。

## コマンド

すべて Laravel Sail（Docker）経由で実行します。Windows の場合は WSL のターミナルで実行します。

```bash
./vendor/bin/sail up -d                        # アプリと MySQL を起動する
./vendor/bin/sail artisan migrate --seed       # テーブルを作り、練習用データを入れる
./vendor/bin/sail artisan migrate:fresh --seed # DB をシード直後の状態に戻す
./vendor/bin/sail artisan test                 # テストをすべて実行する
./vendor/bin/sail artisan test --filter=SomeTest            # クラス・メソッドを指定して実行する
./vendor/bin/sail artisan test tests/Feature/SomeTest.php   # ファイルを指定して実行する
./vendor/bin/sail pint                         # PHP のコードを整形する（Laravel Pint）
./vendor/bin/sail down                         # 停止する
```

`vendor/` が無いときは、先に README.md にある `laravelsail/php82-composer` の docker コマンドでパッケージをインストールします。

テストは MySQL の `testing` データベースを使います（`phpunit.xml` で指定）。このデータベースは、Sail の MySQL コンテナが初回起動時に自動で作ります。今あるテストは `ExampleTest` のひな形だけで、ファクトリも `UserFactory` しかありません（`Post`・`Category` のファクトリはまだありません）。

## アーキテクチャ

- **認証**: Laravel Fortify を使っています（Breeze・Jetstream は使っていません）。有効な機能は会員登録とパスワードリセットだけです（`config/fortify.php`）。ログイン画面・登録画面は `FortifyServiceProvider` で `resources/views/auth/*.blade.php` に結びつけています。ユーザー作成の処理は `app/Actions/Fortify/CreateNewUser.php` にあります。ログイン後の移動先は `/posts` です（`fortify.home` と `RouteServiceProvider::HOME` の両方）。
- **ルート**（`routes/web.php`）: `/` はウェルカムページで、`/posts` のルートはすべて `auth` ミドルウェアのグループの中にあります。あるのは `index`・`edit`・`update`・`destroy` だけで、`create`・`store`・`show` はまだありません。
- **認可**: `PostPolicy`（投稿者本人だけが `update`・`delete` できる）は、Laravel のポリシー自動検出で読み込まれます。`AuthServiceProvider::$policies` が空なのはそのためで、意図どおりです。`PostController` は各アクションで `$this->authorize()` を呼ぶので、他人の投稿を開くと 403 になります。一覧画面（`posts/index.blade.php`）も同じポリシーを `@can` で使い、編集・削除ボタンを出し分けています。
- **バリデーション**: `PostController::update` の中に直接書いています（FormRequest クラスは使っていません）。
- **データモデル**: `users` 1 対多 `posts` 多対 1 `categories`。ユーザーを削除すると `posts.user_id` の投稿も消えます（cascade）。`posts.category_id` は cascade しません。
- **ビュー**: 共通レイアウトの無い、単独の Blade ファイルで、スタイルは各ファイルの `<style>` に直接書いています。Tailwind は使っておらず、Vite のアセットも画面では読み込んでいません。

## 練習用データ

`DatabaseSeeder` は、カテゴリ 3 件（お知らせ・技術メモ・雑記）、ユーザー 7 人（パスワードはすべて `password`）、投稿 25 件を作ります。投稿の `created_at` は現在時刻からの経過時間で決めているので、一覧の並び順は毎回同じです。次の 2 つの検証用アカウントは、メールアドレスとパスワードを変えないでください（シーダーのコメントにもそう書いてあります）。

| メールアドレス | 名前 | 投稿 ID |
|:--|:--|:--|
| `usera@example.com` | はるか | 1, 6, 13, 20 |
| `userb@example.com` | だいち | 2, 9, 16, 23 |

投稿者本人と他人とで動きが変わるか（他人の `/posts/{id}/edit` が 403 になるか）は、この 2 つで確かめます。なお、README.md の練習用データの説明（「2 人・投稿 4 件・`/posts/1`〜`/posts/4`」）は古くなっています。シーダーの中身を正としてください。
