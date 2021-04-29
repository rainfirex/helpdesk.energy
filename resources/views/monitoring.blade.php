<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Мониторинг заявки</title>
    <link rel="shortcut icon" href="{{asset('img/favicon.ico')}}">    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
    <script type="text/javascript" src="{{asset('js/jquery-3.4.1.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/bootstrap.min.js')}}"></script>
    <style>
        .info{
            background: #d9dfe4;
        }
        .comment-your {
            border-radius: 3px;
            background: #f5f3f3;
            min-width: 400px;
        }
    </style>
</head>
<body>
@if($ticket)
    <div class="offset-1 col-md-10 mt-4">
        <div class="info p-4">
            <h3>№ {{ $ticket->number }}</h3>
            <h4><b>Заголовок заявки: </b>"{{ $ticket->title }}"</h4>
            <hr>
            <p><b>Состояние заявки:</b> {{ $ticket->statusTicket->title }}</p>
            <p><b>Исполнитель:</b> {{ ($ticket->performerUser) ?  $ticket->performerUser->name: 'Исполнитель не назначен'}}</p>
            <p>Контактный номер: {{ ($ticket->performerUser) ? $ticket->performerUser->phone : ''}}</p>
            <p><b>Отдел заявителя:</b> {{ $ticket->department }}</p>
            <div class="mt-2 text-center">
                <a class="btn btn-info" href="https://helpdesk.sakh.dvec.ru/detale-ticket/{{ $ticket->number }}">Перейти к заявки</a>
            </div>
        </div>
        @if(count($ticket->comments) > 0)
            <div class=" m-4">
                <p class="text-center"><b>Последние комментарии:</b></p>
            </div>
            @foreach($ticket->comments as $comment)
                <div class="comment-your p-2 m-2">
                    <p class="m-1 comment-created">{{ $comment->created_at }}</p>
                    <p class="m-0"><span class="nick">Вы: </span> {{ $comment->description }}</p>
                </div>
            @endforeach
        @endif
    </div>
@endif
@if($err)
    <div class="offset-1 col-md-10 mt-4">
        <p class="text-center"><b>{{ $err }}</b></p>
        <div class="mt-2 text-center">
            <a class="btn btn-info" href="/">Перейти в систему</a>
        </div>
    </div>
@endif
</body>
</html>
