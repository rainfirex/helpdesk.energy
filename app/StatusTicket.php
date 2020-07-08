<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusTicket extends Model
{
    const ST_UNTOUCHED = 1; // Не начато
    const ST_PERFORMED = 2; // В процессе
    const ST_COMPLETED = 3; // Выполнено
    const ST_REJECTED  = 4; // Отклонено

    protected $fillable = [
        'status', 'title'
    ];

}
