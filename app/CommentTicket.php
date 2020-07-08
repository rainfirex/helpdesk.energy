<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentTicket extends Model
{
    protected $fillable = [
        'user_id',
        'ticket_id',
        'description'
    ];

    public function user() {

    }

    public function ticket() {

    }
}
