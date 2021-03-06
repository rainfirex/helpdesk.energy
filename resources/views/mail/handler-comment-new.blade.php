<style>
    p{
        font-size: 18px;
    }
    .about{
        font-style: italic;
    }
    .about p{
        font-size: 14px;
    }
</style>

<div>
    <div>
        <p>{{$username}}, сотрудник ОИТ {{$user_comment}} добавил комментарий к вашей заявки:</p>
        <p>"{{$description}}"</p>
        <p>Номер заявки: <b>{{$ticket_number}}</b></p>
        <p>По номеру можно отслеживать заявку на странице <a href="http://helpdesk.sakh.dvec.ru/monitor-ticket">"Мониторинг заявки"</a>.</p>
        <hr>
        <div class="about">
            <p>С уважением,<br>
                Робот филиала ПАО «ДЭК» «Сахалинэнергосбыт»<br>
                Система заявок <a href="http://helpdesk.sakh.dvec.ru">helpdesk.sakh.dvec.ru</a></p>
        </div>
    </div>
</div>
