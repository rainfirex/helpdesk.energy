<?php

namespace App\Http\Controllers;

use App\ResourceAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use PDF;

class CResourceAccess extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->only('index', 'show');
    }

    public function index() {
        $count = ResourceAccess::orderBy('id', 'DESC')->count();
        $resources = ResourceAccess::orderBy('id', 'DESC')->get();
        return response()->json(compact('resources', 'count'));
    }

    public function show(int $id){
        $resource = ResourceAccess::find($id);
        return response()->json(compact('resource'));
    }

    public function store(Request $request)
    {
        $user = json_decode($request->input('user'));

        set_time_limit(1000);
        ini_set('memory_limit', '2024m');

//        $pdf = App::make('dompdf.wrapper');
//        $pdf->loadHTML(
//            '<html lang="ru">
//                <head>
//                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
//                <title>PDF</title>
//                <style>
//                  body { font-family: DejaVu Sans, sans-serif; }
//                </style>
//                </head>
//                <body>'.$html.'</body>
//                </html>');
//        return $pdf->save('invoice.pdf')->download('invoice.pdf');

        $pdf = PDF::loadView('pdf', [
            'user' => $user
        ]);

        date_default_timezone_set("Asia/Sakhalin");
        $currDateTime = date('d.m.Y_His');
        $file = $currDateTime . '_access.pdf';


        $dirStorageApp = storage_path('app/public/');
        $dirAccess = 'access_pdf/';
        $dirAccessToday = $dirStorageApp . $dirAccess . date('d.m.Y');

        if (!file_exists($dirStorageApp . $dirAccess)) {
            mkdir($dirStorageApp . $dirAccess);
            chmod($dirStorageApp . $dirAccess, 0777);
        }

        if (!file_exists($dirAccessToday)) {
            mkdir($dirAccessToday);
            chmod($dirAccessToday, 0777);
        }

        $pdf->save($dirAccessToday . '/' . $file);

        $result = ResourceAccess::create([
            'name' => $user->name,
            'leader' => $user->leader,
            'function' => $user->function,
            'unit' => $user->unit,
            'address' => $user->address,
            'cabinet' => $user->cabinet,
            'phone' => $user->phone,
            'perStart' => $user->perStart,
            'perEnd' => $user->perEnd,
            'isLogin' => $user->access->isLogin,
            'is1CUPP' => $user->access->is1CUPP,
            'is1CZPP' => $user->access->is1CZPP,
            'isAsuse' => $user->access->isAsuse,
            'isOmnius' => $user->access->isOmnius,
            'isUSB' => $user->access->isUSB,
            'isFolderObmen' => $user->access->isFolderObmen,
            'isWorkFromUTD' => $user->access->isWorkFromUTD,
            'isEmail' => $user->access->isEmail,
            'isInternet' => $user->access->isInternet,
            'isConsult' => $user->access->isConsult,
            'lanResource' => $user->access->lanResource,
            'otherProgram' => $user->access->otherProgram,
            'pdf_path' => $dirAccessToday . '/' . $file,
            'pdf_url' => asset('storage/' . $dirAccess . date('d.m.Y') . '/' . $file)
        ]);

        return response()->json([
            'success' => $result
        ]);

        return $pdf->download('invoice.pdf');
    }
}