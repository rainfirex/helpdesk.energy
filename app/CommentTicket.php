<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommentTicket extends Model
{
    protected $fillable = [
        'user_id',
        'ticket_id',
        'description',
        'is_handler',
        'is_new'
    ];

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function ticket() {

    }
}
