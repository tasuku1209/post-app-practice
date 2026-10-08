<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ポスト / つぶやき投稿アプリ</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Hiragino Sans", "Noto Sans JP", sans-serif; background: #FFFFFF; color: #0F1419; font-feature-settings: "palt"; }
        a { text-decoration: none; color: inherit; }
        .col { max-width: 600px; margin: 0 auto; min-height: 100dvh; border-left: 1px solid #EFF1F4; border-right: 1px solid #EFF1F4; }
        .chrome { position: sticky; top: 0; z-index: 10; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid #EFF1F4; }
        .chrome-row { display: flex; justify-content: space-between; align-items: center; gap: 0.8rem; padding: 0.6rem 1rem; }
        .brand { display: flex; align-items: center; gap: 0.5rem; font-weight: 800; font-size: 1.05rem; letter-spacing: -0.02em; }
        .brand .mark { width: 28px; height: 28px; border-radius: 38%; background: linear-gradient(135deg, #FFB03A, #FF7A59); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; flex-shrink: 0; }
        .me { display: flex; align-items: center; gap: 0.7rem; min-width: 0; }
        .me .name { font-size: 0.8rem; color: #5B6570; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .logout-btn { background: #FFFFFF; color: #0F1419; border: 1px solid #D3D9DE; padding: 0.38rem 1rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700; cursor: pointer; font-family: inherit; flex-shrink: 0; }
        .logout-btn:hover { background: #F7F8F9; }
        .logout-btn:focus-visible { outline: 2px solid #E8792B; outline-offset: 2px; }
        .page-title { display: flex; align-items: center; gap: 0.8rem; padding: 0.55rem 1rem 0.7rem; font-weight: 800; font-size: 1.06rem; }
        .back { color: #5B6570; font-size: 0.82rem; font-weight: 600; }
        .back:hover { color: #0F1419; text-decoration: underline; text-underline-offset: 3px; }
        .back:focus-visible { outline: 2px solid #E8792B; outline-offset: 2px; border-radius: 4px; }
        .post { display: flex; gap: 0.75rem; padding: 0.9rem 1rem; border-bottom: 1px solid #EFF1F4; }
        .avatar { width: 44px; height: 44px; border-radius: 999px; flex-shrink: 0; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; }
        .reply .avatar { width: 36px; height: 36px; font-size: 0.95rem; }
        .post-body { flex: 1; min-width: 0; }
        .post-head { display: flex; align-items: baseline; gap: 0.3rem; flex-wrap: wrap; }
        .post-head .name { font-weight: 700; font-size: 0.94rem; }
        .post-head .time { color: #5B6570; font-size: 0.82rem; font-variant-numeric: tabular-nums; }
        .post-head .time::before { content: "・"; margin-right: 0.05rem; color: #98A1A8; }
        .topic { margin-left: auto; border-radius: 999px; padding: 0.1rem 0.6rem; font-size: 0.7rem; font-weight: 700; }
        .post-title { font-weight: 700; font-size: 0.96rem; margin: 0.28rem 0 0.06rem; }
        .post-text { font-size: 0.94rem; line-height: 1.7; color: #0F1419; overflow-wrap: anywhere; }
        .post-actions { display: flex; gap: 1.2rem; margin-top: 0.5rem; }
        .post-actions button { background: none; border: none; padding: 0; color: #5B6570; font-size: 0.8rem; font-weight: 600; cursor: pointer; font-family: inherit; }
        .post-actions button:hover { color: #D6402C; text-decoration: underline; text-underline-offset: 3px; }
        .post-actions button:focus-visible { outline: 2px solid #E8792B; outline-offset: 2px; border-radius: 4px; }
        form.composer { padding: 1rem; border-bottom: 1px solid #EFF1F4; }
        label { display: block; color: #5B6570; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 700; }
        textarea { width: 100%; min-height: 90px; padding: 0.72rem 0.85rem; border: 1px solid #D3D9DE; border-radius: 10px; font-size: 0.95rem; font-family: inherit; background: #fff; color: #0F1419; resize: vertical; line-height: 1.7; }
        textarea:focus { outline: none; border-color: #0F1419; box-shadow: 0 0 0 3px rgba(232, 121, 43, 0.18); }
        .error { color: #D6402C; font-size: 0.83rem; margin-top: 0.3rem; }
        .actions { display: flex; justify-content: flex-end; margin-top: 0.8rem; }
        .btn { padding: 0.55rem 1.5rem; border-radius: 999px; font-size: 0.9rem; font-weight: 700; cursor: pointer; font-family: inherit; background: #0F1419; color: #fff; border: none; }
        .btn:hover { background: #272C30; }
        .btn:focus-visible { outline: 2px solid #E8792B; outline-offset: 2px; }
        .empty { color: #5B6570; text-align: center; padding: 3rem 1rem; }
    </style>
</head>
<body>
    <div class="col">
        <header class="chrome">
            <div class="chrome-row">
                <a href="{{ route('posts.index') }}" class="brand"><span class="mark">つ</span>つぶやき投稿アプリ</a>
                <div class="me">
                    <span class="name">{{ auth()->user()->name }}（{{ auth()->user()->email }}）</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-btn">ログアウト</button>
                    </form>
                </div>
            </div>
            <div class="page-title">
                <a href="{{ route('posts.index') }}" class="back">← ホームに戻る</a>
                <span>ポスト</span>
            </div>
        </header>

        <main>
            @php
                // アイコンの色は登録した名前から決まる（crc32 % 18）
                $grad = [
                    ['#5B8DEF', '#3F5FD0'],
                    ['#E8A23D', '#C97F1B'],
                    ['#34B58B', '#1E8A6E'],
                    ['#E05252', '#B93245'],
                    ['#8E6AE0', '#6748BE'],
                    ['#3FB0C9', '#2884A5'],
                    ['#F2865C', '#DB5A34'],
                    ['#6D7BE0', '#4A55C2'],
                    ['#8FAE3E', '#6C8A26'],
                    ['#31B183', '#188064'],
                    ['#7C93B5', '#56718F'],
                    ['#F7A934', '#E8721F'],
                    ['#EF6A6A', '#CE3A50'],
                    ['#9B6CE8', '#6E48C9'],
                    ['#E45FA3', '#C13A85'],
                    ['#B58A5C', '#8F653A'],
                    ['#55A8E2', '#3380BC'],
                    ['#4E9E8E', '#2F7A6B'],
                ];
                $topicTone = [
                    'お知らせ' => ['#FFF3DC', '#8A5714'],
                    '技術メモ' => ['#E8F1FD', '#2F5AA8'],
                    '雑記' => ['#F0F1F3', '#57606A'],
                ];
                [$g1, $g2] = $grad[crc32($post->user->name) % count($grad)];
                [$tBg, $tFg] = $topicTone[$post->category->name] ?? ['#F0F1F3', '#57606A'];
            @endphp

            <article class="post">
                <div class="avatar" style="background: linear-gradient(135deg, {{ $g1 }}, {{ $g2 }});">{{ mb_substr($post->user->name, 0, 1) }}</div>
                <div class="post-body">
                    <div class="post-head">
                        <span class="name">{{ $post->user->name }}</span>
                        <span class="time">{{ $post->created_at->format('n月j日 H:i') }}</span>
                        <span class="topic" style="background: {{ $tBg }}; color: {{ $tFg }};">{{ $post->category->name }}</span>
                    </div>
                    <p class="post-title">{{ $post->title }}</p>
                    <p class="post-text">{{ $post->content }}</p>
                </div>
            </article>

            <form class="composer" action="{{ route('replies.store', $post) }}" method="POST">
                @csrf
                <label for="content">リプライ</label>
                <textarea id="content" name="content" placeholder="返信を入力">{{ old('content') }}</textarea>
                @error('content')
                    <p class="error">{{ $message }}</p>
                @enderror
                <div class="actions">
                    <button type="submit" class="btn">返信する</button>
                </div>
            </form>

            @if ($replies->isEmpty())
                <p class="empty">まだリプライがありません。</p>
            @else
                @foreach ($replies as $reply)
                    @php
                        [$r1, $r2] = $grad[crc32($reply->user->name) % count($grad)];
                    @endphp
                    <article class="post reply">
                        <div class="avatar" style="background: linear-gradient(135deg, {{ $r1 }}, {{ $r2 }});">{{ mb_substr($reply->user->name, 0, 1) }}</div>
                        <div class="post-body">
                            <div class="post-head">
                                <span class="name">{{ $reply->user->name }}</span>
                                <span class="time">{{ $reply->created_at->format('n月j日 H:i') }}</span>
                            </div>
                            <p class="post-text">{{ $reply->content }}</p>
                            @can('delete', $reply)
                                <div class="post-actions">
                                    <form action="{{ route('replies.destroy', $reply) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">削除</button>
                                    </form>
                                </div>
                            @endcan
                        </div>
                    </article>
                @endforeach
            @endif
        </main>
    </div>
</body>
</html>
