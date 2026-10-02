# post-app

投稿を一覧・編集・削除できる、小さな Laravel アプリです。Tutorial 13 から 15 まで、このアプリ 1 本を題材に設計・実装・仕組み化を進めます。

## できること

- ユーザー登録・ログイン・ログアウト
- 投稿の一覧表示（投稿者の名前とカテゴリつき）
- 投稿の編集（タイトル・カテゴリ・本文）と削除
- 自分の投稿だけを編集・削除（他人の投稿は編集・削除ボタンが出ず、URL を直接開くと 403 になります）

## フォルダについて

`answers/` は Tutorial 13 の答え合わせ用で、アプリのコードとは関係ありません。設計書の解答例が入っているだけなので、アプリの動きには影響しません。詳しくは `answers/README.md` を見てください。

自分で作る設計書は `docs/` に置きます（Tutorial 13 の中で作ります）。

## 使用技術

| 項目 | 内容 |
|:-----|:-----|
| フレームワーク | Laravel 10 |
| 言語 | PHP |
| データベース | MySQL |
| 実行環境 | Laravel Sail（Docker） |
| 認証 | Laravel Fortify |

PHP と MySQL のバージョンは Sail のコンテナが決めます。手元で確かめるときは `./vendor/bin/sail php -v` と `./vendor/bin/sail mysql --version` を実行してください。

## セットアップ

Docker Desktop（または Docker Engine）を起動してから実行してください。Windows の方は WSL（Ubuntu）のターミナルで実行します。

```bash
# パッケージをインストールする
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install

# 環境ファイルを作る
cp .env.example .env

# Sail を起動する
./vendor/bin/sail up -d

# アプリケーションキーを作る
./vendor/bin/sail artisan key:generate

# テーブルを作り、練習用データを入れる
./vendor/bin/sail artisan migrate --seed
```

ブラウザで `http://localhost` を開くと、トップページが表示されます。

初回起動時は `migrate` で「Connection refused」が出ることがあります。MySQL の起動が終わっていないだけなので、少し待ってからもう一度実行してください。

## 練習用のアカウント

`migrate --seed` で、カテゴリ 3 件（お知らせ・技術メモ・雑記）と、次の 2 人、それぞれの投稿 2 件（計 4 件）が入ります。

| メールアドレス | パスワード | 投稿 |
|:---------------|:-----------|:-----|
| `usera@example.com` | `password` | `/posts/1`・`/posts/2` |
| `userb@example.com` | `password` | `/posts/3`・`/posts/4` |

本物の値は入っていません。すべて練習用のダミーです。

## テスト

```bash
./vendor/bin/sail artisan test
```

## 停止

```bash
./vendor/bin/sail down
```
