<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocFile extends Model
{
    protected $fillable = [
        'path', 'url', 'name', 'mime_type', 'user_id', 'ticket_id'
    ];
}
