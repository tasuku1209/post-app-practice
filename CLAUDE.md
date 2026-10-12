# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## プロジェクトの概要

`post-app` は、Laravel 10 で作った小さな教材アプリです（認証は Fortify）。Tutorial 13 から 15 まで、このアプリ 1 本を共通の題材として使います。README やコード内のコメントは日本語なので、画面の文言・入力チェック（バリデーション）のメッセージ・ドキュメントも日本語で書いてください。

- `answers/` は Tutorial 13 の設計書の解答例（Markdown と draw.io）です。アプリのコードとは関係ないので、アプリの作業では読んだり変更したりしないでください。
- `docs/` は受講者が自分の設計書を置く場所です（Tutorial 13 の中で作るので、まだ無いこともあります）。

## コマンド

すべて Laravel Sail（Docker）経由で実行します。Windows の場合は WSL のターミナルで実行します。

```bash
./vendor/bin/sail up -d                        # アプリと MySQL を起動する
./vendor/bin/sail artisan migrate --seed       # テーブルを作り、練習用データを入れる
./vendor/bin/sail artisan migrate:fresh --seed # DB をシード直後の状態に戻す
./vendor/bin/sail test                         # テストをすべて実行する
./vendor/bin/sail test --filter=SomeTest       # クラス・メソッドを指定して実行する
./vendor/bin/sail pint                         # PHP のコードを整形する（Laravel Pint）
./vendor/bin/sail down                         # 停止する
```

`vendor/` が無いときは、先に README.md にある `laravelsail/php82-composer` の docker コマンドでパッケージをインストールします。

テストは MySQL の `testing` データベースを使います（`phpunit.xml` で指定）。このデータベースは、Sail の MySQL コンテナが初回起動時に自動で作ります。

## 実装の方針

- **認証**: Laravel Fortify を使います（Breeze・Jetstream は使いません）。
- **認可**: 「誰がその操作をしてよいか」は Policy に書きます。Controller の各アクションで `$this->authorize()` を呼び、画面のボタンの出し分けにも同じ Policy を `@can` で使います。条件を Controller や Blade に直接書かないでください。
- **バリデーション**: Controller の中に直接書きます（FormRequest は使いません）。

## 練習用データ

`DatabaseSeeder` が練習用のデータを作ります。次の 2 つの検証用アカウントは、メールアドレスとパスワード（`password`）を変えないでください（シーダーのコメントにもそう書いてあります）。投稿者本人と他人とで動きが変わるか（他人の投稿を編集しようとして 403 になるか）は、この 2 つで確かめます。

- `usera@example.com`
- `userb@example.com`

## 開発の進め方

### テスト

- 機能を足す・直すときは、テストも書きます（`tests/Feature/`）。
- `./vendor/bin/sail test` で全部のテストを回し、落ちたら通るまで直します。最後に、結果（通った件数と、直した点）を報告します。

### プルリクエスト

- PR の本文に、テスト結果と、受け入れ条件との突き合わせを書きます。受け入れ条件を 1 つずつ挙げて、どのテスト（または画面操作）で確かめたかを添えます。
