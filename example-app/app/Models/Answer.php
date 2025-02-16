<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = ['answer', 'true_answer', 'question_id'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'question_id');
    }
}
