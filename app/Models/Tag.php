<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function news()
    {
        return $this->belongsToMany(News::class, 'news_has_tags');
    }

    public function myNews()
    {
        return $this->belongsToMany(MyNews::class, 'news_has_tags');
    }
}
