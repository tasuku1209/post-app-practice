<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ポストを編集 / つぶやき投稿アプリ</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Hiragino Sans", "Noto Sans JP", sans-serif; background: #FFFFFF; color: #0F1419; font-feature-settings: "palt"; }
        a { text-decoration: none; color: inherit; }
        .col { max-width: 600px; margin: 0 auto; min-height: 100dvh; border-left: 1px solid #EFF1F4; border-right: 1px solid #EFF1F4; }
        .chrome { position: sticky; top: 0; z-index: 10; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid #EFF1F4; }
        .chrome-row { display: flex; align-items: center; padding: 0.6rem 1rem; }
        .brand { display: flex; align-items: center; gap: 0.5rem; font-weight: 800; font-size: 1.05rem; letter-spacing: -0.02em; }
        .brand .mark { width: 28px; height: 28px; border-radius: 38%; background: linear-gradient(135deg, #FFB03A, #FF7A59); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: 800; flex-shrink: 0; }
        .page-title { padding: 0.55rem 1rem 0.7rem; font-weight: 800; font-size: 1.06rem; }
        form.editor { padding: 1.2rem 1rem 1.6rem; }
        .form-group { margin-bottom: 1.15rem; }
        label { display: block; color: #5B6570; margin-bottom: 0.4rem; font-size: 0.8rem; font-weight: 700; }
        input[type="text"], textarea, select { width: 100%; padding: 0.72rem 0.85rem; border: 1px solid #D3D9DE; border-radius: 10px; font-size: 0.95rem; font-family: inherit; background: #fff; color: #0F1419; }
        textarea { min-height: 140px; resize: vertical; line-height: 1.7; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #0F1419; box-shadow: 0 0 0 3px rgba(232, 121, 43, 0.18); }
        .actions { display: flex; gap: 0.7rem; margin-top: 1.4rem; }
        .btn { padding: 0.66rem 1.7rem; border-radius: 999px; font-size: 0.9rem; font-weight: 700; cursor: pointer; text-align: center; font-family: inherit; }
        .btn-primary { background: #0F1419; color: #fff; border: none; }
        .btn-primary:hover { background: #272C30; }
        .btn-secondary { background: #FFFFFF; color: #0F1419; border: 1px solid #D3D9DE; }
        .btn-secondary:hover { background: #F7F8F9; }
        .btn:focus-visible { outline: 2px solid #E8792B; outline-offset: 2px; }
        .error { color: #D6402C; font-size: 0.83rem; margin-top: 0.3rem; }
    </style>
</head>
<body>
    <div class="col">
        <header class="chrome">
            <div class="chrome-row">
                <a href="{{ route('posts.index') }}" class="brand"><span class="mark">つ</span>つぶやき投稿アプリ</a>
            </div>
            <div class="page-title">ポストを編集</div>
        </header>

        <main>
            <form class="editor" action="{{ route('posts.update', $post) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title">タイトル</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                    @error('title')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category_id">トピック</label>
                    <select id="category_id" name="category_id" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="content">本文</label>
                    <textarea id="content" name="content" required>{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">更新</button>
                    <a href="{{ route('posts.index') }}" class="btn btn-secondary">キャンセル</a>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
