<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'content',
    ];

    // 作ったあとに更新された（= 一度でも編集された）ポストかどうか。列を足さず、日時の比較で判定する
    public function isEdited(): bool
    {
        return $this->updated_at->gt($this->created_at);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class);
    }
}
