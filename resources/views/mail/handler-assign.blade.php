<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
</head>
<body>
    <p>{{ $currUser }}, назначил вас исполнителем заявке <b>№ {{ $ticketNumber  }} </b></p>
    <p><a href="https://helpdesk.sakh.dvec.ru/handler-tickets/detale-ticket/{{ $ticketId }}">Перейти к заявке</a></p>
</body>
</html>