<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    protected $fillable = ['user_id', 'company_id', 'original_url', 'short_code'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}