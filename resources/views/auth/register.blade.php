<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アカウント作成 / つぶやき投稿アプリ</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Hiragino Sans", "Noto Sans JP", sans-serif; background: #F4F5F6; color: #0F1419; min-height: 100dvh; display: flex; justify-content: center; align-items: center; padding: 1.2rem; font-feature-settings: "palt"; }
        .card { width: 100%; max-width: 400px; background: #FFFFFF; border: 1px solid #EFF1F4; border-radius: 20px; padding: 2.3rem 2.1rem 2.1rem; box-shadow: 0 18px 44px -24px rgba(15, 20, 25, 0.22); }
        .brand { display: flex; align-items: center; justify-content: center; gap: 0.55rem; font-weight: 800; font-size: 1.4rem; letter-spacing: -0.02em; margin-bottom: 0.4rem; }
        .brand .mark { width: 40px; height: 40px; border-radius: 38%; background: linear-gradient(135deg, #FFB03A, #FF7A59); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: 800; }
        h1 { font-size: 0.92rem; text-align: center; color: #5B6570; font-weight: 500; margin-bottom: 1.7rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; color: #5B6570; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 700; }
        input { width: 100%; padding: 0.78rem 0.9rem; border: 1px solid #D3D9DE; border-radius: 10px; font-size: 0.95rem; background: #FFFFFF; color: #0F1419; font-family: inherit; }
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
        <h1>アカウントを作成する</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label for="name">名前</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
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

            <div class="form-group">
                <label for="password_confirmation">パスワード（確認）</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit">登録</button>
        </form>

        <div class="links">
            登録済みですか？ <a href="{{ route('login') }}">ログイン</a>
        </div>
    </div>
</body>
</html>
