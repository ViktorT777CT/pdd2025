<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionNumber extends Model
{
    protected $fillable = ['number'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
