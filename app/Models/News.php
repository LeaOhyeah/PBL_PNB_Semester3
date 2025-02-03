<?php

namespace App\Models;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Pgvector\Laravel\HasNeighbors;
use Pgvector\Laravel\Vector;

class News extends Model
{
    use HasFactory;
    // use HasNeighbors;
    // use HasPanelShield;

    protected $guarded = ['id'];

    // protected $casts = [
    //     'embedding' => 'array',
    // ];

    // protected $casts = ['embedding' => Vector::class];

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
        return $this->belongsToMany(Tag::class, 'news_has_tags');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($news) {
            $news->id = (string) Str::uuid();
        });
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }
}
