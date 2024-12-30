<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class MyNews extends Model
{
    use HasFactory;

    protected $table = 'news';  // Menghubungkan model MyNews ke tabel news
    protected $guarded = ['id'];

    protected $keyType = 'string';
    public $incrementing = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'news_has_tags', 'news_id', 'tag_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($news) {
            $news->id = (string) Str::uuid();

            // Otomatis verifikasi jika user adalah admin atau super_admin
            if (Auth::check() && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('super_admin'))) {
                $news->verified_at = now()->addSeconds(10);
            }
        });

        // Global Scope untuk user_id
        static::addGlobalScope('user_id', function ($builder) {
            if (Auth::check()) {
                $builder->where('user_id', Auth::id());
            }
        });
    }
}
