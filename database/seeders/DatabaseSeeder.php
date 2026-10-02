<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // トピック（カテゴリ）
        $notice = Category::create(['name' => 'お知らせ']);
        $tech = Category::create(['name' => '技術メモ']);
        $etc = Category::create(['name' => '雑記']);

        // 検証用アカウント（メールとパスワードは変更しない）
        $haruka = User::create([
            'name' => 'はるか',
            'email' => 'usera@example.com',
            'password' => Hash::make('password'),
        ]);

        $daichi = User::create([
            'name' => 'だいち',
            'email' => 'userb@example.com',
            'password' => Hash::make('password'),
        ]);

        $mio = User::create(['name' => 'みお', 'email' => 'mio@example.com', 'password' => Hash::make('password')]);
        $sota = User::create(['name' => 'そうた', 'email' => 'sota@example.com', 'password' => Hash::make('password')]);
        $yui = User::create(['name' => 'ゆい', 'email' => 'yui@example.com', 'password' => Hash::make('password')]);
        $kento = User::create(['name' => 'けんと', 'email' => 'kento@example.com', 'password' => Hash::make('password')]);
        $akari = User::create(['name' => 'あかり', 'email' => 'akari@example.com', 'password' => Hash::make('password')]);

        // ポスト（古い順に定義し、created_at は現在からの経過時間で固定する）
        $posts = [
            [$haruka, $notice, 'はじめまして', 'つぶやき投稿、はじめました。思ったことをぽつぽつ置いていきます。よろしくお願いします。', 200],
            [$daichi, $tech, 'マイグレーションの向き', 'up と down を書く意味がやっと腹落ちした。戻せる変更として書くと安心感が全然違う。', 187],
            [$mio, $etc, '朝のルーティン', 'コーヒーを淹れてから机に座るまでが一番気持ちいい時間かもしれない。', 170],
            [$sota, $tech, 'N+1にはまった', '一覧画面が急に重くなって調べたら N+1 だった。with() を1個足すだけで直るの、拍子抜けする。', 166],
            [$yui, $etc, '散歩日和', '午後から近所を1時間歩いた。歩いてる間に詰まってたバグの原因を思いついたのはなぜ。', 150],
            [$haruka, $tech, 'バリデーションの置き場所', 'コントローラーに書くかFormRequestに分けるか迷い中。画面が増えてきたら分ける、でいいのかな。', 141],
            [$kento, $etc, '今日の失敗', 'seeder を直したのに migrate だけ実行して「変わらない！」と10分悩んだ。fresh --seed でした。', 133],
            [$akari, $notice, '読書メモはじめます', '読んだ本の要点をトピック「お知らせ」じゃなくて雑記に置くべきだった気もする。まあいいか。', 120],
            [$daichi, $etc, '深夜のラーメン', '我慢できずに食べてしまった。後悔はしていない。明日の自分がなんとかする。', 111],
            [$mio, $tech, 'ルートの命名', 'route(\'posts.index\') みたいに名前で呼べるようにしておくと、URLを変えてもリンクが壊れないの偉い。', 100],
            [$sota, $etc, '観葉植物を買った', 'デスクに緑があるだけで作業のやる気が2割増える気がする。枯らさないように頑張る。', 92],
            [$yui, $tech, 'ミドルウェアのイメージ', '駅の改札みたいなものだと理解した。ホームに入る前に切符を確認する場所。auth はまさにそれ。', 85],
            [$haruka, $etc, '雨の日の勉強', '外が雨だと集中できる説、自分にはけっこう当てはまる。今日は進んだ。', 74],
            [$kento, $tech, 'ポリシーを書いた', '「自分のポストだけ編集できる」を if 文の散らばりじゃなくて Policy に集めるの、読みやすくて良い。', 66],
            [$akari, $etc, '週末の予定', '土曜は友だちとカフェ巡り。日曜は溜まった課題を消化する日にする。宣言することで自分を縛る。', 58],
            [$daichi, $notice, '勉強会やります', '来週の金曜、オンラインでLaravelのもくもく会をやります。初心者歓迎です。気軽にどうぞ。', 49],
            [$mio, $etc, '猫が邪魔をする', 'キーボードの上に乗ってくるのでペアプロの相手だと思うことにした。無言のレビュアー。', 41],
            [$sota, $notice, 'プロフィール画像の話', 'アイコンの色が名前で決まるの、地味に気に入っている。私はずっとこの色でいたい。', 36],
            [$yui, $etc, '早起きチャレンジ3日目', '3日続いた。えらい。ご褒美に少しいいパンを買った。明日も起きられるかは別の話。', 26],
            [$haruka, $tech, 'テストを先に書いてみた', '先にテストを書くと「何ができたら完成か」を先に決めることになるんだな。設計の練習にもなる気がする。', 20],
            [$kento, $etc, '夕焼けがすごかった', 'ベランダから見えた空が完全に優勝していた。写真では伝わらないやつ。', 13],
            [$akari, $tech, 'コミットの粒度', '「あとで読む人が困らない大きさ」って言われて納得した。未来の自分も他人。', 9],
            [$daichi, $tech, 'ログの読み方が変わった', 'エラー画面を上から全部読むのをやめて、最初の1行と自分のコードの行だけ探すようにしたら速くなった。', 5],
            [$mio, $notice, '今週の振り返り', '進んだこと: 認可の仕組みが分かった。来週: 一覧画面の使い勝手をもう少し良くしたい。', 3],
            [$sota, $etc, '休憩宣言', '2時間集中したのでアイス食べます。溶ける前に食べ切るまでが休憩です。', 1],
        ];

        foreach ($posts as [$user, $category, $title, $content, $hoursAgo]) {
            Post::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => $title,
                'content' => $content,
                'created_at' => now()->subHours($hoursAgo),
                'updated_at' => now()->subHours($hoursAgo),
            ]);
        }
    }
}
