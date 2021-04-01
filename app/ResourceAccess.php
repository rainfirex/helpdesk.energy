<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResourceAccess extends Model
{
    protected $fillable = [
        'name', 'leader', 'function','unit', 'address','cabinet', 'phone', 'perStart','perEnd',
        'isLogin', 'is1CUPP', 'is1CZPP', 'isAsuse', 'isOmnius', 'isUSB', 'isFolderObmen', 'isWorkFromUTD', 'isEmail', 'isInternet', 'isConsult',
        'lanResource', 'otherProgram', 'pdf_path', 'pdf_url'
    ];
}
