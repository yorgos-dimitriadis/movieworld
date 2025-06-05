<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    public function user() {
      return $this->belongsTo(User::class);
    }

    public function votes() {
      return $this->hasMany(Vote::class);
    }

    public function likes() {
      return $this->votes()->where('type', 'like');
    }

    public function hates() {
      return $this->votes()->where('type', 'hate');
    }

}
