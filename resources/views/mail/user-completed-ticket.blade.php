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
        <p>{{$username}}, вы <b>завершили</b> заявку: "{{$title}}".</p>
        <p>Номер: <b>{{$ticket_number}}</b></p>
        <hr>
        <div class="about">
            <p>С уважением,<br>
                Робот филиала ПАО «ДЭК» «Сахалинэнергосбыт»<br>
                Система заявок <a href="http://helpdesk.sakh.dvec.ru">helpdesk.sakh.dvec.ru</a></p>
        </div>
    </div>
</div>
