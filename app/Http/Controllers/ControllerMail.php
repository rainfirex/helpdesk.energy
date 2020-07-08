<?php

namespace App\Http\Controllers;

use App\Mail\FeedMail;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ControllerMail extends Controller
{

    public function send() {

        ini_set('memory_limit', '1024m');

        $emails = [];
//        $emails_devc = [
//            'Troitskaya-ON@sakh.dvec.ru',
//            'Poplavskiy-AA@sakh.dvec.ru',
//            'Shaposhnikov-SM@sakh.dvec.ru',
//            'Matskevichus-AA@sakh.dvec.ru',
//            'Belonogov-AA@sakh.dvec.ru',
//            'Gvan-DM@sakh.dvec.ru',
//            'Shishkov-DE@sakh.dvec.ru',
//            'Litin-DV@sakh.dvec.ru',
//            'Solodkov-NN@sakh.dvec.ru'
//        ];

        $filename = storage_path("app/files/email.txt");
        $resource = fopen(($filename), "r");
        while(!feof($resource)) {
            $emails[] = trim(fgets($resource));
        }

        foreach ($emails as $email){

//            Mail::to($email)->send(new FeedMail(''));

            sleep(2);
        }
        return 'Отработано!';
    }
}
