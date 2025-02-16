<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketNumber extends Model
{
    protected $fillable = ['number'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
