<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン / つぶやき投稿アプリ</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Hiragino Sans", "Noto Sans JP", sans-serif; background: #F4F5F6; color: #0F1419; min-height: 100dvh; display: flex; justify-content: center; align-items: center; padding: 1.2rem; font-feature-settings: "palt"; }
        .card { width: 100%; max-width: 400px; background: #FFFFFF; border: 1px solid #EFF1F4; border-radius: 20px; padding: 2.3rem 2.1rem 2.1rem; box-shadow: 0 18px 44px -24px rgba(15, 20, 25, 0.22); }
        .brand { display: flex; align-items: center; justify-content: center; gap: 0.55rem; font-weight: 800; font-size: 1.4rem; letter-spacing: -0.02em; margin-bottom: 0.4rem; }
        .brand .mark { width: 40px; height: 40px; border-radius: 38%; background: linear-gradient(135deg, #FFB03A, #FF7A59); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 800; }
        h1 { font-size: 0.92rem; text-align: center; color: #5B6570; font-weight: 500; margin-bottom: 1.7rem; }
        .test-accounts { background: #F7F8F9; border: 1px solid #EFF1F4; padding: 0.85rem 1rem; border-radius: 14px; margin-bottom: 1.5rem; font-size: 0.82rem; }
        .test-accounts h3 { color: #5B6570; margin-bottom: 0.35rem; font-size: 0.72rem; letter-spacing: 0.07em; }
        .test-accounts p { color: #0F1419; margin: 0.15rem 0; font-variant-numeric: tabular-nums; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; color: #5B6570; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 700; }
        input[type="email"], input[type="password"] { width: 100%; padding: 0.78rem 0.9rem; border: 1px solid #D3D9DE; border-radius: 10px; font-size: 0.95rem; background: #FFFFFF; color: #0F1419; font-family: inherit; }
        input:focus { outline: none; border-color: #0F1419; box-shadow: 0 0 0 3px rgba(232, 121, 43, 0.18); }
        button { width: 100%; padding: 0.85rem; background: #0F1419; color: #fff; border: none; border-radius: 999px; font-size: 0.95rem; font-weight: 700; cursor: pointer; margin-top: 0.7rem; font-family: inherit; }
        button:hover { background: #272C30; }
        button:focus-visible { outline: 2px solid #E8792B; outline-offset: 2px; }
        .error { color: #D6402C; font-size: 0.83rem; margin-top: 0.3rem; }
        .links { text-align: center; margin-top: 1.4rem; font-size: 0.88rem; color: #5B6570; }
        .links a { color: #0F1419; text-decoration: underline; text-underline-offset: 3px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="card">
        <div class="brand"><span class="mark">つ</span>つぶやき投稿アプリ</div>
        <h1>いま思ったことを、ひとこと。</h1>

        <div class="test-accounts">
            <h3>テストアカウント</h3>
            <p><strong>ユーザーA:</strong> usera@example.com / password</p>
            <p><strong>ユーザーB:</strong> userb@example.com / password</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">ログイン</button>
        </form>

        <div class="links">
            はじめてですか？ <a href="{{ route('register') }}">アカウントを作成</a>
        </div>
    </div>
</body>
</html>
