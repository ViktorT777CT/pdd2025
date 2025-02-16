<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = ['question', 'comment', 'image', 'category_id', 'ticket_number_id', 'question_number_id'];

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id');
    }
}
