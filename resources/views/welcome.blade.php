<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>つぶやき投稿アプリ</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Hiragino Sans", "Noto Sans JP", sans-serif; background: #FFFFFF; color: #0F1419; min-height: 100dvh; display: flex; justify-content: center; align-items: center; padding: 1.2rem; font-feature-settings: "palt"; }
        .card { width: 100%; max-width: 384px; text-align: center; }
        .mark { width: 76px; height: 76px; border-radius: 38%; background: linear-gradient(135deg, #FFB03A, #FF7A59); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2.3rem; font-weight: 800; margin: 0 auto 1.2rem; box-shadow: 0 18px 40px -18px rgba(232, 121, 43, 0.5); }
        .wordmark { font-weight: 800; font-size: 1.9rem; letter-spacing: -0.02em; margin-bottom: 0.35rem; }
        .tagline { color: #5B6570; margin-bottom: 2.3rem; }
        .user-info { background: #F7F8F9; border: 1px solid #EFF1F4; padding: 0.9rem 1rem; border-radius: 14px; margin-bottom: 1.3rem; font-size: 0.9rem; color: #0F1419; }
        .links { display: flex; flex-direction: column; gap: 0.7rem; }
        .links a { display: block; padding: 0.85rem 1.5rem; border-radius: 999px; text-decoration: none; font-size: 0.95rem; font-weight: 700; }
        .btn-primary { background: #0F1419; color: #fff; }
        .btn-primary:hover { background: #272C30; }
        .btn-secondary { background: #FFFFFF; color: #0F1419; border: 1px solid #D3D9DE; }
        .btn-secondary:hover { background: #F7F8F9; }
        .btn-danger { background: #FFFFFF; color: #D6402C; border: 1px solid #EBD5CE; padding: 0.85rem 1.5rem; border-radius: 999px; font-size: 0.95rem; font-weight: 700; cursor: pointer; width: 100%; font-family: inherit; }
        .btn-danger:hover { background: #FCF4F2; border-color: #D6402C; }
        .links a:focus-visible, .btn-danger:focus-visible { outline: 2px solid #E8792B; outline-offset: 2px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="mark">つ</div>
        <div class="wordmark">つぶやき投稿アプリ</div>
        <p class="tagline">いま思ったことを、ひとこと。</p>

        @auth
            <div class="user-info"><strong>ログイン中:</strong> {{ auth()->user()->name }}</div>
            <div class="links">
                <a href="{{ route('posts.index') }}" class="btn-primary">タイムラインへ</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-danger">ログアウト</button>
                </form>
            </div>
        @else
            <div class="links">
                <a href="{{ route('login') }}" class="btn-primary">ログイン</a>
                <a href="{{ route('register') }}" class="btn-secondary">アカウントを作成</a>
            </div>
        @endauth
    </div>
</body>
</html>
