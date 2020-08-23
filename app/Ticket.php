<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'number', 'phone', 'department', 'category', 'title', 'description', 'status_id', 'performer_user_id', 'is_new'
    ];

    public function statusTicket() {
        return $this->hasOne(StatusTicket::class, 'id', 'status_id');
    }

    public function comments() {
        return $this->hasMany(CommentTicket::class, 'ticket_id', 'id');
    }

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function performerUser() {
        return $this->hasOne(User::class, 'id', 'performer_user_id');
    }

    public function isNewUserComment() {
        return $this->hasMany(CommentTicket::class, 'ticket_id', 'id')
            ->where('is_new', '=', '1')
            ->where('is_handler', '=', '1');
    }

    public function isNewHandlerComment() {
        return $this->hasMany(CommentTicket::class, 'ticket_id', 'id')
            ->where('is_new', '=', '1')
            ->where('is_handler', '=', '0');
    }
}
